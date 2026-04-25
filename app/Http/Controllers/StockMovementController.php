<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index()
    {
        return StockMovement::with(['product', 'user'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|string',
            'reference' => 'nullable|string',
            'quantity' => 'required|integer',
            'previous_stock' => 'required|integer',
            'new_stock' => 'required|integer',
            'reason' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        return StockMovement::create($data);
    }

    public function show(StockMovement $stockMovement)
    {
        return $stockMovement;
    }

    public function update(Request $request, StockMovement $stockMovement)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|string',
            'reference' => 'nullable|string',
            'quantity' => 'required|integer',
            'previous_stock' => 'required|integer',
            'new_stock' => 'required|integer',
            'reason' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $stockMovement->update($data);

        return $stockMovement;
    }

    public function destroy(StockMovement $stockMovement)
    {
        $stockMovement->delete();

        return response()->noContent();
    }
}
