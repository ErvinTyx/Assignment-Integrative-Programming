<?php
// app/Listeners/AutoShipOrderListener.php

namespace App\Listeners;

use App\Models\Order;
use App\Events\PaymentSuccessful;

class AutoShipOrderListener
{
    public function handle(PaymentSuccessful $event)
    {
        $order = Order::where('payment_intent', $event->paymentIntentId)->first();

        if ($order && $order->status === \App\Enums\OrderStatusEnum::Paid) {
            $order->proceed(); // Move to 'shipped'
        }
    }
}
