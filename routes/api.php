<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderApiController;
use App\Http\Controllers\OrderApiItemController;
use App\Http\Controllers\ProductApiController;
use App\Http\Controllers\CartApiController;
use App\Http\Controllers\VendorApiController;
use App\Http\Controllers\ProfileApiController;
use App\Http\Controllers\StripeApiController;


//vendor api
Route::get('/vendor', [VendorApiController::class, 'index'])->name('api.vendors.index');
Route::get('/vendor/{vendor}', [VendorApiController::class, 'show'])->name('api.vendors.show');
Route::post('/vendor', [VendorApiController::class, 'store'])->name('api.vendors.store');
Route::put('/vendor/{vendor}', [VendorApiController::class, 'update'])->name('api.vendors.update');
Route::delete('/vendor/{vendor}', [VendorApiController::class, 'destroy'])->name('api.vendors.destroy');

//cart api
Route::get('/cart', [CartApiController::class, 'index'])->name('api.cart.index');
Route::post('/cart/{product}', [CartApiController::class, 'store'])->name('api.cart.store');
Route::put('/cart/{product}', [CartApiController::class, 'update'])->name('api.cart.update');
Route::delete('/cart/{product}', [CartApiController::class, 'destroy'])->name('api.cart.destroy');


//product api
Route::get('/products', [ProductApiController::class, 'index'])->name('api.products.index');
Route::get('/products/{id}', [ProductApiController::class, 'show'])->name('api.products.show');
Route::post('/products', [ProductApiController::class, 'store'])->name('api.products.store');
Route::put('/products/{id}', [ProductApiController::class, 'update'])->name('api.products.update');
Route::delete('/products/{id}', [ProductApiController::class, 'destroy'])->name('api.products.destroy');
Route::get('/products-by-vendor', [ProductApiController::class, 'index'])->name('api.products.by-vendor');


//orderapi
Route::get('/orders', [OrderApiController::class, 'index'])->name('api.orders.index');
Route::get('/orders/{id}', [OrderApiController::class, 'show'])->name('api.orders.show');
Route::post('/orders', [OrderApiController::class, 'store'])->name('api.orders.store');
Route::put('/orders/{id}', [OrderApiController::class, 'update'])->name('api.orders.update');
Route::delete('/orders/{id}', [OrderApiController::class, 'destroy'])->name('api.orders.destroy');
Route::put('/orders/{id}/fail', [OrderApiController::class, 'fail'])->name('orders.fail');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileApiController::class, 'show']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::delete('/profile', [ProfileApiController::class, 'destroy']);
});



Route::post('/success', [StripeApiController::class, 'success']);
Route::post('/failure', [StripeApiController::class, 'failure']);
Route::post('/webhook', [StripeApiController::class, 'webhook']);
Route::post('/connect', [StripeApiController::class, 'connect']);
Route::post('/payout/{vendorId}', [StripeApiController::class, 'payout']);

use App\Http\Controllers\AuthApiController;

Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');

