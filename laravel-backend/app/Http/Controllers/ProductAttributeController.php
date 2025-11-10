<?php

namespace App\Http\Controllers;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductAttributeController extends Controller
{
    public function index()
    {
        try {
            return response()->json(ProductAttribute::with('values')->get(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve attributes', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate incoming request
            $validated = $request->validate([
                'name' => 'required|string|unique:product_attributes,name',
                'values' => 'required|array',
                'values.*' => 'required|string|max:255',
            ]);

            // Create product attribute
            $attribute = ProductAttribute::create([
                'name' => $validated['name'],
            ]);

            // Create product attribute values
            foreach ($validated['values'] as $value) {
                ProductAttributeValue::create([
                    'product_attribute_id' => $attribute->id,
                    'value' => $value,
                ]);
            }

            // Commit transaction
            DB::commit();

            return response()->json($attribute->load('values'), 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of error
            return response()->json(['error' => 'Failed to create attribute', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(ProductAttribute $productAttribute)
    {
        try {
            return response()->json($productAttribute->load('values'), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product attribute not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve attribute', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, ProductAttribute $productAttribute)
    {
        DB::beginTransaction();

        try {
            // Validate incoming request
            $validated = $request->validate([
                'name' => 'sometimes|required|string|unique:product_attributes,name,' . $productAttribute->id,
                'values' => 'required|array',
                'values.*' => 'required|string|max:255',
            ]);

            // Update product attribute
            $productAttribute->update([
                'name' => $validated['name'],
            ]);

            // Delete old values and add new ones
            $productAttribute->values()->delete();

            foreach ($validated['values'] as $value) {
                ProductAttributeValue::create([
                    'product_attribute_id' => $productAttribute->id,
                    'value' => $value,
                ]);
            }

            // Commit transaction
            DB::commit();

            return response()->json($productAttribute->load('values'), 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction on error
            return response()->json(['error' => 'Failed to update attribute', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        DB::beginTransaction();

        try {
            // Delete the attribute values and the attribute itself
            $productAttribute->values()->delete();
            $productAttribute->delete();

            // Commit transaction
            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction on error
            return response()->json(['error' => 'Failed to delete attribute', 'message' => $e->getMessage()], 500);
        }
    }
}