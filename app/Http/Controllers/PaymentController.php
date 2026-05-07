<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        return Payment::with(['invoice', 'user', 'cashRegister'])->paginate(20);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'method' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($data['invoice_id']);
        $cashRegister = CashRegister::where('user_id', $user->id)->where('status', 'open')->first();

        if (! $cashRegister) {
            return response()->json(['message' => 'Debe abrir una caja antes de registrar pagos.'], 422);
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_date' => $data['payment_date'],
            'amount' => $data['amount'],
            'method' => $data['method'] ?? null,
            'reference' => $data['reference'] ?? null,
            'user_id' => $user->id,
            'cash_register_id' => $cashRegister->id,
        ]);

        CashTransaction::create([
            'cash_register_id' => $cashRegister->id,
            'invoice_id' => $invoice->id,
            'payment_id' => $payment->id,
            'type' => 'sale',
            'method' => $payment->method,
            'amount' => $payment->amount,
            'reference' => $payment->reference,
            'note' => 'Pago registrado para factura ' . $invoice->id,
        ]);

        $invoice->refresh();
        $invoice->updatePaymentStatus();

        return $payment->load(['invoice', 'user', 'cashRegister']);
    }

    public function show(Payment $payment)
    {
        return $payment->load(['invoice', 'user', 'cashRegister']);
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'method' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $payment->update($data);
        $payment->invoice->updatePaymentStatus();

        return $payment->load(['invoice', 'user', 'cashRegister']);
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $payment->delete();
        $invoice->updatePaymentStatus();

        return response()->noContent();
    }
}
