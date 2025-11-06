<?php

namespace App\Http\Controllers;

use App\Models\ProductVariationAttribute;
use App\Models\ProductVariation;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;

class ProductVariationAttributeController extends Controller
{
    /**
     * Display a listing of all variation–attribute links.
     */
    public function index()
    {
        $data = ProductVariationAttribute::with(['variation.product', 'attributeValue.attribute'])
            ->latest()
            ->paginate(10);

        return response()->json($data);
    }

    /**
     * Store a new product variation–attribute value relationship.
     *
     * Expected JSON:
     * {
     *   "product_variation_id": 1,
     *   "product_attribute_value_id": 3
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_variation_id' => 'required|exists:product_variations,id',
            'product_attribute_value_id' => 'required|exists:product_attribute_values,id',
        ]);

        // Ensure the combination doesn’t already exist
        $exists = ProductVariationAttribute::where([
            'product_variation_id' => $validated['product_variation_id'],
            'product_attribute_value_id' => $validated['product_attribute_value_id']
        ])->exists();

        if ($exists) {
            return response()->json(['error' => 'This variation already has the selected attribute value.'], 409);
        }

        $link = ProductVariationAttribute::create($validated);

        return response()->json([
            'message' => 'Attribute value assigned to product variation successfully',
            'data' => $link->load(['variation.product', 'attributeValue.attribute'])
        ], 201);
    }

    /**
     * Display a specific variation–attribute link.
     */
    public function show($id)
    {
        $link = ProductVariationAttribute::with(['variation.product', 'attributeValue.attribute'])->findOrFail($id);
        return response()->json($link);
    }

    /**
     * Update a specific variation–attribute link.
     *
     * Expected JSON:
     * {
     *   "product_variation_id": 2,
     *   "product_attribute_value_id": 5
     * }
     */
    public function update(Request $request, $id)
    {
        $link = ProductVariationAttribute::findOrFail($id);

        $validated = $request->validate([
            'product_variation_id' => 'sometimes|exists:product_variations,id',
            'product_attribute_value_id' => 'sometimes|exists:product_attribute_values,id',
        ]);

        $link->update($validated);

        return response()->json([
            'message' => 'Variation–attribute link updated successfully',
            'data' => $link->load(['variation.product', 'attributeValue.attribute']),
        ]);
    }

    /**
     * Remove a variation–attribute link.
     */
    public function destroy($id)
    {
        $link = ProductVariationAttribute::findOrFail($id);
        $link->delete();

        return response()->json(['message' => 'Variation–attribute link removed successfully']);
    }
}
