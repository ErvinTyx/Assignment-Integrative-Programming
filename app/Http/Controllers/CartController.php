<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Service\CartService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CartService $cartService)
    {
        return Inertia::render('Cart/Index', [
            'cartItems' => $cartService->getCartItemsGrouped(),
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
   // app/Http/Controllers/Api/CartApiController.php (store)
    public function store(Request $request, Product $product, CartService $cartService)
    {
        $request->mergeIfMissing(['quantity' => 1]);

        $data = $request->validate([
            'option_ids' => ['nullable', 'array'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        // Pass product id (int) — not the whole model
        $cartService->addItemToCart(
            $product->id,
            $data['quantity'],
            $data['option_ids'] ?? []
        );

        return response()->json(['success' => true, 'message' => 'Product added to cart'], 201);
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, CartService $cartService)
    {
        $request->validate([
            'quantity' => ['integer', 'min:1'],
        ]);

        $optionIds = $request->input('option_ids') ?: [];// Get the option_ids from the request

        $quantity = $request->input('quantity');

        $cartService->updateItemQuantity(
            $product->id,
            $quantity,
            $optionIds
        );

        return back()->with('success', 'Quantity was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product, CartService $cartService)
    {
        $optionIds = $request->input('option_ids');

        $cartService->removeItemFromCart($product->id, $optionIds);

        return back()->with('success', 'Product was removed from cart');
    }

    public function checkout(Request $request, CartService $cartService)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $vendorId = $request->input('vendor_id');

        $allCartItems = $cartService->getCartItemsGrouped();

        DB::beginTransaction();

        try {
            $checkoutCartItems = $allCartItems;
            if ($vendorId) {
                $checkoutCartItems = [$allCartItems[$vendorId]];
            }
            $orders = [];
            $lineItems = [];
            foreach ($checkoutCartItems as $item) {
                $user = $item['user'];
                $cartItems = $item['items'];

                try {
                        // Auto-detect: if request has 'use_api' query param, consume externally
                        $useApi = $request->query('use_api', false);

                        if ($useApi) {
                            // External API consumption (Order Module REST API)
                            $response = Http::timeout(10)->post(route('api.orders.store'), [
                                'stripe_session_id' => null,
                                'user_id'           => $request->user()->id,
                                'vendor_user_id'    => $user['id'],
                                'total_price'       => $item['totalPrice'],
                                'status'            => OrderStatusEnum::Draft->value,
                            ]);

                            if ($response->failed()) {
                                throw new \Exception('Failed to create order via API');
                            }

                            $order = $response->json(); // API response (JSON object/array)

                        } else {
                            // Internal DB call (same module, no API)
                            $order = Order::create([
                                'stripe_session_id' => null,
                                'user_id'           => $request->user()->id,
                                'vendor_user_id'    => $user['id'],
                                'total_price'       => $item['totalPrice'],
                                'status'            => OrderStatusEnum::Draft->value,
                            ]);
                        }

                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 500);
                }


                $orders[] = $order;

                $orders[] = $order;

                foreach ($cartItems as $cartItem) {
                    try {
                        // Always use internal DB call
                        $orderItem = OrderItem::create([
                            'order_id'                  => is_array($order) ? $order['id'] : $order->id,
                            'product_id'                => $cartItem['product_id'],
                            'quantity'                  => $cartItem['quantity'],
                            'price'                     => $cartItem['price'],
                            'variation_type_option_ids' => $cartItem['option_ids'],
                        ]);

                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => $e->getMessage(),
                        ], 500);
                    }

                    $description = collect($cartItem['options'])->map(function ($item) {
                        return "{$item['type']['name']}: {$item['name']}";
                    })->implode(', ');

                    $lineItem = [
                        'price_data' => [
                            'currency' => config('app.currency'),
                            'product_data' => [
                                'name' => $cartItem['title'],
                                'images' => [$cartItem['image']],
                            ],
                            'unit_amount' => $cartItem['price'] * 100,
                        ],
                        'quantity' => $cartItem['quantity'],
                    ];

                    if ($description) {
                        $lineItem["price_data"]["product_data"]["description"] = $description;
                    }


                    $lineItems[] = $lineItem;
                }
            }

            $session = Session::create([
                'customer_email' => $request->user()->email,
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success', []) . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('stripe.failure', []) ."?session_id={CHECKOUT_SESSION_ID}",
            ]);

            foreach ($orders as $order) {
                $order->stripe_session_id = $session->id;
                $order->save();
            }

            DB::commit();

            return redirect($session->url);

        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return back()->with('error', $e->getMessage() ?: "Something went wrong");
        }
    }
}
