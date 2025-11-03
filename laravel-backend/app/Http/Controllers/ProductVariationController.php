<?php

namespace App\Http\Controllers;

use App\Models\ProductVariation;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductVariationController extends Controller
{
    public function index()
    {
        return ProductVariation::with('product', 'attributeVariations')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $variation = ProductVariation::create($validated);

        // Add the attribute variations to the product variation
        if ($request->has('attribute_variations')) {
            $variation->attributeVariations()->attach($request->input('attribute_variations'));
        }

        return response()->json($variation, 201);
    }

    public function show(ProductVariation $productVariation)
    {
        return $productVariation->load('product', 'attributeVariations');
    }

    public function update(Request $request, ProductVariation $productVariation)
    {
        $validated = $request->validate([
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
        ]);

        $productVariation->update($validated);

        // Add or update the attribute variations for this product variation
        if ($request->has('attribute_variations')) {
            $productVariation->attributeVariations()->sync($request->input('attribute_variations'));
        }

        return response()->json($productVariation, 200);
    }

    public function destroy(ProductVariation $productVariation)
    {
        $productVariation->delete();

        return response()->json(null, 204);
    }
}
