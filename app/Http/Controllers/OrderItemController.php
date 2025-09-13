<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function store(Request $request)
    {
        $orderItem = OrderItem::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'variation_type_option_ids' => $request->variation_type_option_ids,
        ]);

        return response()->json([
            'success' => true,
            'data' => $orderItem,
        ], 201);
    }
}
