<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderViewResource;
use Illuminate\Validation\Rule;

class OrderApiController extends Controller
{
    /**
     * List all orders or filter by user/vendor
     * GET /api/orders?user_id=1&vendor_user_id=2
     */
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('vendor_user_id')) {
            $query->where('vendor_user_id', $request->vendor_user_id);
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get a single order
     * GET /api/orders/{id}
     */
    public function show($id)
    {
        try {
            $order = Order::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    /**
     * Create a new order
     * POST /api/orders
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'stripe_session_id' => 'nullable|string',
            'user_id' => 'required|integer|exists:users,id',
            'vendor_user_id' => 'required|integer|exists:users,id',
            'total_price' => 'required|numeric',
            'status' => ['required', 'string', Rule::in(['draft','paid','failed','cancelled'])],
        ]);

        $order = Order::create($data);

        return response()->json([
            'success' => true,
            'data' => $order
        ], 201);
    }

    /**
     * Update an existing order
     * PUT /api/orders/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $data = $request->validate([
                'stripe_session_id' => 'nullable|string',
                'total_price' => 'sometimes|required|numeric',
                'status' => ['sometimes', 'required', Rule::in(['draft','paid','failed','cancelled'])],
            ]);

            $order->update($data);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    /**
     * Delete an order
     * DELETE /api/orders/{id}
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }
}
