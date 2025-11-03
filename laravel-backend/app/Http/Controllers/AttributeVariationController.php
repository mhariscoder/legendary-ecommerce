<?php

namespace App\Http\Controllers;

use App\Models\AttributeVariation;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class AttributeVariationController extends Controller
{
    public function index()
    {
        return AttributeVariation::with('attribute')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_attribute_id' => 'required|exists:product_attributes,id',
            'value' => 'required|string',
        ]);

        $variation = AttributeVariation::create($validated);

        return response()->json($variation, 201);
    }

    public function show(AttributeVariation $attributeVariation)
    {
        return $attributeVariation->load('attribute');
    }

    public function update(Request $request, AttributeVariation $attributeVariation)
    {
        $validated = $request->validate([
            'value' => 'sometimes|required|string',
        ]);

        $attributeVariation->update($validated);

        return response()->json($attributeVariation, 200);
    }

    public function destroy(AttributeVariation $attributeVariation)
    {
        $attributeVariation->delete();

        return response()->json(null, 204);
    }
}
