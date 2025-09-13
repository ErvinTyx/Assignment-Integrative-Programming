<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;

Route::post('/orders', [OrderController::class, 'store'])
    ->name('api.orders.store');

Route::post('/order-items', [OrderItemController::class, 'store'])
    ->name('api.order-items.store');
