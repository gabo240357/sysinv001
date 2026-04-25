<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return Payment::with('invoice')->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'method' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        return Payment::create($data);
    }

    public function show(Payment $payment)
    {
        return $payment;
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

        return $payment;
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->noContent();
    }
}
