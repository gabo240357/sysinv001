<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with(['category', 'supplier', 'tax'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sku' => 'nullable|string|unique:products,sku',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'stock_minimum' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'nullable|string',
            'created_by' => 'nullable|exists:users,id',
        ]);

        return Product::create($data);
    }

    public function show(Product $product)
    {
        return $product->load(['category', 'supplier', 'tax']);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku' => 'nullable|string|unique:products,sku,'.$product->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'stock_minimum' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'nullable|string',
            'created_by' => 'nullable|exists:users,id',
        ]);

        $product->update($data);

        return $product->fresh(['category', 'supplier', 'tax']);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}
