<?php

namespace App\States;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Exception;

class CancelledState implements OrderStateInterface
{
    public function proceed(Order $order): void
    {
        throw new Exception("Order was cancelled. Cannot proceed.");
    }


    public function cancel(Order $order): void
    {
         throw new Exception("Order was cancelled.");
    }
}