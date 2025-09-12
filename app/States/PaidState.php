<?php

namespace App\States;


use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Exception;


class PaidState implements OrderStateInterface
    {
    public function proceed(Order $order): void
    {
        $order->status = OrderStatusEnum::Shipped;
        $order->save();
    }


    public function cancel(Order $order): void
    {
        throw new Exception("Cannot cancel an order that has been paid.");
    }
}