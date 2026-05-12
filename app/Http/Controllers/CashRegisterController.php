<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashRegisterController extends Controller
{
    public function index()
    {
        return CashRegister::with(['user', 'transactions.invoice', 'transactions.payment'])->paginate(20);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

        if (CashRegister::where('user_id', $user->id)->where('status', 'open')->exists()) {
            return response()->json(['message' => 'Ya existe una caja abierta para este usuario.'], 422);
        }

        $data = $request->validate([
            'initial_amount' => 'required|numeric|min:0',
            'open_note' => 'nullable|string',
        ]);

        $cashRegister = CashRegister::create([
            'user_id' => $user->id,
            'initial_amount' => $data['initial_amount'],
            'status' => 'open',
            'open_note' => $data['open_note'] ?? null,
            'opened_at' => now(),
        ]);

        $cashRegister->transactions()->create([
            'type' => 'opening',
            'amount' => $cashRegister->initial_amount,
            'note' => $cashRegister->open_note,
        ]);

        return $cashRegister->load(['user', 'transactions.invoice', 'transactions.payment']);
    }

    public function show(CashRegister $cashRegister)
    {
        return $cashRegister->load(['user', 'transactions']);
    }

    public function update(Request $request, CashRegister $cashRegister)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Usuario no autenticado.'], 401);
        }

        $data = $request->validate([
            'closing_amount' => 'required|numeric|min:0',
            'close_note' => 'nullable|string',
        ]);

        if ($cashRegister->status !== 'open') {
            return response()->json(['message' => 'La caja ya está cerrada.'], 422);
        }

        try {
            $cashRegister->update([
                'closing_amount' => $data['closing_amount'],
                'close_note' => $data['close_note'] ?? null,
                'status' => 'closed',
                'closed_at' => now()->toDateTimeString(),
            ]);

            $cashRegister->transactions()->create([
                'type' => 'closing',
                'amount' => $cashRegister->closing_amount,
                'note' => $cashRegister->close_note,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al cerrar la caja: ' . $e->getMessage()], 500);
        }

        return $cashRegister->load(['user', 'transactions.invoice', 'transactions.payment']);
    }

    public function destroy(CashRegister $cashRegister)
    {
        $cashRegister->delete();

        return response()->noContent();
    }
}
