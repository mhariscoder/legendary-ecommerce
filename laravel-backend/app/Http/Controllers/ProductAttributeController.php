<?php

namespace App\Http\Controllers;

use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index()
    {
        return ProductAttribute::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:product_attributes,name',
        ]);

        $attribute = ProductAttribute::create($validated);

        return response()->json($attribute, 201);
    }

    public function show(ProductAttribute $productAttribute)
    {
        return $productAttribute;
    }

    public function update(Request $request, ProductAttribute $productAttribute)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|unique:product_attributes,name,' . $productAttribute->id,
        ]);

        $productAttribute->update($validated);

        return response()->json($productAttribute, 200);
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        $productAttribute->delete();

        return response()->json(null, 204);
    }
}
