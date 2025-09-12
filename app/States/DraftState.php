<?php

namespace App\States;


use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Exception;


class DraftState implements OrderStateInterface
    {
    public function proceed(Order $order): void
    {
        $order->status = OrderStatusEnum::Paid;
        $order->save();
    }


    public function cancel(Order $order): void
    {
        $order->status = OrderStatusEnum::Cancelled;
        $order->save();
    }
}