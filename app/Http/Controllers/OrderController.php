<?php


// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderViewResource;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function proceedOrder($id)
    {
        $order = Order::findOrFail($id);

        try {
            $order->proceed();
            return back()->with('success', 'Order status updated.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancelOrder($id)
    {
        $order = Order::findOrFail($id);

        try {
            $order->cancel();
            return back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        return inertia('Orders/Show', [
            'order' => (new OrderViewResource($order))->toArray(request()),
        ]);
    }

}
