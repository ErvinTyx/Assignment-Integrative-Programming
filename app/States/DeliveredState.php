<?php

namespace App\States;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Exception;

class DeliveredState implements OrderStateInterface
{
    public function proceed(Order $order): void
    {
       throw new Exception("Order already delivered. No further actions.");
    }


    public function cancel(Order $order): void
    {
        throw new Exception("Cannot cancel a delivered order.");
    }
}