<?php

// app/Services/OrderWorkflowService.php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderService
{
    public function autoAdvance(Order $order): void
    {
        // Automatically move to next stage
        $order->proceed();
    }

    public function forceCancel(Order $order): void
    {
        // Automatically cancel (e.g. timeout, fraud detected)
        $order->cancel();
    }
}
