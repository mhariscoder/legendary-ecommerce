<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display all orders.
     */
    public function index()
    {
        $orders = Order::with(['customer', 'products', 'invoice'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * Store a new order with its products.
     *
     * Expected JSON:
     * {
     *   "customer_id": 1,
     *   "shipping_address": "123 Main St",
     *   "payment_method": "credit_card",
     *   "products": [
     *     { "product_id": 1, "quantity": 2 },
     *     { "product_id": 3, "quantity": 1 }
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'shipping_address' => 'required|string',
            'payment_method' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            $productData = [];

            foreach ($validated['products'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $lineTotal = $product->price * $item['quantity'];
                $total += $lineTotal;

                $productData[$product->id] = [
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];

                // Reduce stock
                $product->decrement('stock', $item['quantity']);
            }

            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'shipping_address' => $validated['shipping_address'],
                'payment_method' => $validated['payment_method'] ?? null,
                'total_price' => $total,
                'status' => 'pending',
            ]);

            // Attach products
            $order->products()->attach($productData);

            // Generate invoice
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'total_amount' => $total,
                'status' => 'unpaid',
                'issue_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(7),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['customer', 'products', 'invoice']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Order creation failed',
                'details' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show a specific order.
     */
    public function show($id)
    {
        $order = Order::with(['customer', 'products', 'invoice'])->findOrFail($id);
        return response()->json($order);
    }

    /**
     * Update an existing order (e.g., change status or address).
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|string',
            'shipping_address' => 'sometimes|string',
            'payment_method' => 'nullable|string',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order->load(['customer', 'products', 'invoice']),
        ]);
    }

    /**
     * Delete an order and its invoice.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
