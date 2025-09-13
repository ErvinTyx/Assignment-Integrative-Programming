<?php

namespace App\States;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Exception;

class FailedState implements OrderStateInterface
{
    public function proceed(Order $order): void
    {
        // From failed, you normally cannot proceed
        throw new Exception("A failed order cannot proceed further.");
    }

    public function cancel(Order $order): void
    {
        // You may allow failed orders to be marked as cancelled
        $order->status = OrderStatusEnum::Cancelled;
        $order->save();
    }
}
