<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        return Tax::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'percentage' => 'required|numeric|min:0',
            'apply_sales' => 'nullable|boolean',
            'apply_purchase' => 'nullable|boolean',
        ]);

        return Tax::create($data);
    }

    public function show(Tax $tax)
    {
        return $tax;
    }

    public function update(Request $request, Tax $tax)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'percentage' => 'required|numeric|min:0',
            'apply_sales' => 'nullable|boolean',
            'apply_purchase' => 'nullable|boolean',
        ]);

        $tax->update($data);

        return $tax;
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();

        return response()->noContent();
    }
}
