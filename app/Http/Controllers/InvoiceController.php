<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::with(['customer', 'items.product', 'payments'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'nullable|string',
            'series' => 'nullable|string',
            'number' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string',
            'customer_document' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        $items = $data['items'];
        unset($data['items']);

        $data['subtotal'] = 0;
        $data['tax_total'] = 0;
        $data['discount'] = $data['discount'] ?? 0;
        $data['status'] = $data['status'] ?? 'due';
        $data['user_id'] = $data['user_id'] ?? auth()->id();

        DB::transaction(function () use (&$data, $items, &$invoice) {
            $invoice = Invoice::create($data);

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $discount = $item['discount'] ?? 0;
                $amount = ($quantity * $unitPrice) - $discount;
                $taxRate = $product->tax?->percentage ?? 16;
                $taxAmount = round($amount * ($taxRate / 100), 2);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'amount' => $amount,
                    'tax_rate' => $taxRate,
                ]);

                $data['subtotal'] += $amount;
                $data['tax_total'] += $taxAmount;

                $previousStock = $product->stock;
                $product->decrement('stock', $quantity);
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'reference' => 'invoice:'.$invoice->id,
                    'quantity' => $quantity,
                    'previous_stock' => $previousStock,
                    'new_stock' => $product->stock,
                    'reason' => 'Venta registrada',
                    'user_id' => $data['user_id'] ?? null,
                ]);
            }

            $invoice->update([
                'subtotal' => $data['subtotal'],
                'tax_total' => $data['tax_total'],
                'total' => $data['subtotal'] + $data['tax_total'] - $data['discount'],
            ]);
        });

        return $invoice->load(['customer', 'items.product', 'payments']);
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load(['customer', 'items.product', 'payments']);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'type' => 'nullable|string',
            'series' => 'nullable|string',
            'number' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string',
            'customer_document' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $invoice->update($data);

        return $invoice->fresh(['customer', 'items.product', 'payments']);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return response()->noContent();
    }
}
