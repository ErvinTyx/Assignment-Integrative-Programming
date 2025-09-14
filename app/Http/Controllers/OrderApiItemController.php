<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderItemApiController extends Controller
{
    /**
     * List all order items or filter by order
     * GET /api/order-items?order_id=1
     */
    public function index(Request $request)
    {
        $query = OrderItem::query();

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        $orderItems = $query->get();

        return response()->json([
            'success' => true,
            'data' => $orderItems
        ]);
    }

    /**
     * Create a new order item
     * POST /api/order-items
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'variation_type_option_ids' => 'nullable|array',
        ]);

        $orderItem = OrderItem::create($data);

        return response()->json([
            'success' => true,
            'data' => $orderItem,
        ], 201);
    }

    /**
     * Get a specific order item
     * GET /api/order-items/{id}
     */
    public function show($id)
    {
        try {
            $orderItem = OrderItem::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $orderItem
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }
    }

    /**
     * Update an existing order item
     * PUT /api/order-items/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $orderItem = OrderItem::findOrFail($id);

            $data = $request->validate([
                'quantity' => 'sometimes|required|integer|min:1',
                'price' => 'sometimes|required|numeric|min:0',
                'variation_type_option_ids' => 'nullable|array',
            ]);

            $orderItem->update($data);

            return response()->json([
                'success' => true,
                'data' => $orderItem
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }
    }

    /**
     * Delete an order item
     * DELETE /api/order-items/{id}
     */
    public function destroy($id)
    {
        try {
            $orderItem = OrderItem::findOrFail($id);
            $orderItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }
    }
}
