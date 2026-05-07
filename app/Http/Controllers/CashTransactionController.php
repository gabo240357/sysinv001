<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use Illuminate\Http\Request;

class CashTransactionController extends Controller
{
    public function index()
    {
        return CashTransaction::with(['cashRegister.user', 'invoice', 'payment'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'payment_id' => 'nullable|exists:payments,id',
            'type' => 'required|in:opening,closing,sale,cash_in,cash_out,adjustment',
            'method' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        return CashTransaction::create($data);
    }

    public function show(CashTransaction $cashTransaction)
    {
        return $cashTransaction->load(['cashRegister.user', 'invoice', 'payment']);
    }

    public function update(Request $request, CashTransaction $cashTransaction)
    {
        $data = $request->validate([
            'type' => 'nullable|in:opening,closing,sale,cash_in,cash_out,adjustment',
            'method' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $cashTransaction->update($data);

        return $cashTransaction;
    }

    public function destroy(CashTransaction $cashTransaction)
    {
        $cashTransaction->delete();

        return response()->noContent();
    }
}
