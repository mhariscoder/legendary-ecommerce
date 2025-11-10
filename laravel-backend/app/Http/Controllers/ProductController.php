<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * List products with pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);

        $products = Product::with(['variations.attributes'])->paginate($perPage);

        return response()->json([
            'data' => $products->items(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
        ]);
    }

    /**
     * Store a new product with variations and attributes
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'sku' => 'required|string|unique:products,sku',
            'variations' => 'nullable|array',
            'variations.*.price' => 'required|numeric',
            'variations.*.stock' => 'required|integer',
            'variations.*.attribute_values' => 'required|integer'
        ]);

        DB::beginTransaction();

        try {
            // Create product
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'sku' => $validated['sku'],
            ]);

            // Handle variations
            if (!empty($validated['variations'])) {
                foreach ($validated['variations'] as $variationData) {
                    $variation = ProductVariation::create([
                        'product_id' => $product->id,
                        'price' => $variationData['price'],
                        'stock' => $variationData['stock'],
                    ]);

                    // Now attribute_values is a single ID
                    ProductVariationAttribute::create([
                        'product_variation_id' => $variation->id,
                        'product_attribute_value_id' => $variationData['attribute_values'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product->load('variations.attributes'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Product creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a single product with variations
     */
    public function show(Product $product)
    {
        return response()->json($product->load('variations.attributes'));
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $product->id,
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|integer|exists:product_variations,id',
            'variations.*.price' => 'required_with:variations|numeric',
            'variations.*.stock' => 'required_with:variations|integer',
            'variations.*.attribute_values' => 'required_with:variations|string',
        ]);

        DB::beginTransaction();

        try {
            $product->update($validated);

            // Update variations if provided
            if (!empty($validated['variations'])) {
                foreach ($validated['variations'] as $variationData) {
                    $variation = isset($variationData['id'])
                        ? ProductVariation::find($variationData['id'])
                        : ProductVariation::create(['product_id' => $product->id]);

                    $variation->update([
                        'price' => $variationData['price'],
                        'stock' => $variationData['stock'],
                    ]);

                    // Sync attributes
                    $variation->attributes()->delete(); // remove old
                    $attrValues = explode(',', $variationData['attribute_values']);
                    foreach ($attrValues as $attrValue) {
                        [$attributeId, $valueId] = explode('-', $attrValue);
                        ProductVariationAttribute::create([
                            'product_variation_id' => $variation->id,
                            'product_attribute_value_id' => $valueId,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json($product->load('variations.attributes'), 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Product update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 204);
    }
}
