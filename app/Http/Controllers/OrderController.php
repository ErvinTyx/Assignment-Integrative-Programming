<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderViewResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        return inertia('Orders/Show', [
            'order' => (new OrderViewResource($order))->toArray(request()),
        ]);
    }


}
