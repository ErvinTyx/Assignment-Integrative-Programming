<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderApiController;
use App\Http\Controllers\OrderApiItemController;
use App\Http\Controllers\ProductApiController;
use App\Http\Controllers\CartApiController;
use App\Http\Controllers\VendorApiController;
use App\Http\Controllers\ProfileApiController;

//vendor api
Route::prefix('vendors')->group(function () {
    Route::get('/', [VendorApiController::class, 'index'])->name('api.vendors.index');
    Route::get('/{vendor}', [VendorApiController::class, 'show'])->name('api.vendors.show');
    Route::post('/', [VendorApiController::class, 'store'])->name('api.vendors.store');
    Route::put('/{vendor}', [VendorApiController::class, 'update'])->name('api.vendors.update');
    Route::delete('/{vendor}', [VendorApiController::class, 'destroy'])->name('api.vendors.destroy');
});

//cart api
Route::get('/cart', [CartApiController::class, 'index'])->name('api.cart.index');
Route::post('/cart', [CartApiController::class, 'store'])->name('api.cart.store');
Route::put('/cart/{cartItem}', [CartApiController::class, 'update'])->name('api.cart.update');
Route::delete('/cart/{cartItem}', [CartApiController::class, 'destroy'])->name('api.cart.destroy');
Route::post('/cart/remove-purchased', [CartApiController::class, 'removePurchased'])->name('api.cart.remove-purchased');


//product api
/*Route::get('/products', [ProductApiController::class, 'index'])->name('api.products.index');
Route::get('/products/{id}', [ProductApiController::class, 'show'])->name('api.products.show');
Route::post('/products', [ProductApiController::class, 'store'])->name('api.products.store');
Route::put('/products/{id}', [ProductApiController::class, 'update'])->name('api.products.update');
Route::delete('/products/{id}', [ProductApiController::class, 'destroy'])->name('api.products.destroy');
Route::get('/products-by-vendor', [ProductApiController::class, 'index'])->name('api.products.by-vendor');*/

// Public reads
Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/{id}', [ProductApiController::class, 'show']);

// Mutations require auth
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products',  [ProductApiController::class, 'store']);
    Route::put('/products/{id}', [ProductApiController::class, 'update']);
    Route::delete('/products/{id}', [ProductApiController::class, 'destroy']);
});




//orderapi
Route::get('/orders', [OrderApiController::class, 'index'])->name('api.orders.index');
Route::get('/orders/{id}', [OrderApiController::class, 'show'])->name('api.orders.show');
Route::post('/orders', [OrderApiController::class, 'store'])->name('api.orders.store');
Route::put('/orders/{id}', [OrderApiController::class, 'update'])->name('api.orders.update');
Route::delete('/orders/{id}', [OrderApiController::class, 'destroy'])->name('api.orders.destroy');

//order item api
Route::get('/order-items', [OrderItemApiController::class, 'index'])->name('api.order-items.index');
Route::get('/order-items/{id}', [OrderItemApiController::class, 'show'])->name('api.order-items.show');
Route::post('/order-items', [OrderItemApiController::class, 'store'])->name('api.order-items.store');
Route::put('/order-items/{id}', [OrderItemApiController::class, 'update'])->name('api.order-items.update');
Route::delete('/order-items/{id}', [OrderItemApiController::class, 'destroy'])->name('api.order-items.destroy');


Route::get('/profile', [ProfileApiController::class, 'show'])->name('api.profile.show');
Route::put('/profile', [ProfileApiController::class, 'update'])->name('api.profile.update');
Route::delete('/profile', [ProfileApiController::class, 'destroy'])->name('api.profile.destroy');

