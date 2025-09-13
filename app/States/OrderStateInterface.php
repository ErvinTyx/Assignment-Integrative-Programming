<?php

namespace App\States;


use App\Models\Order;


interface OrderStateInterface
{
public function proceed(Order $order): void;
public function cancel(Order $order): void;
}