<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Service\CartService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CartApiController extends Controller
{
    /**
     * Display cart grouped items (JSON).
     */
    public function index(Request $request, CartService $cartService)
    {
        try {
            $grouped = $cartService->getCartItemsGrouped();

            return response()->json([
                'success' => true,
                'data' => $grouped,
            ]);
        } catch (Exception $e) {
            Log::error('Cart index error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart items',
            ], 500);
        }
    }

    /**
     * Add a product to cart.
     */
    public function store(Request $request, Product $product, CartService $cartService)
    {
        $request->mergeIfMissing([
            'quantity' => 1,
        ]);

        $data = $request->validate([
            'option_ids' => ['nullable', 'array'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $cartService->addItemToCart(
                $product->id,
                $data['quantity'],
                $data['option_ids'] ?? [],
            );

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
            ], 201);
        } catch (Exception $e) {
            Log::error('Cart add error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to add product to cart',
            ], 500);
        }
    }

    /**
     * Update cart item quantity or options.
     */
    public function update(Request $request, Product $product, CartService $cartService)
    {
        $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
            'option_ids' => ['nullable', 'array'],
        ]);

        $optionIds = $request->input('option_ids', []);
        $quantity = $request->input('quantity');

        try {
            $cartService->updateItemQuantity(
                $product->id,
                $quantity,
                $optionIds
            );

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated',
            ]);
        } catch (Exception $e) {
            Log::error('Cart update error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to update cart item',
            ], 500);
        }
    }

    /**
     * Remove cart item.
     */
    public function destroy(Request $request, Product $product, CartService $cartService)
    {
        $optionIds = $request->input('option_ids', []);

        try {
            $cartService->removeItemFromCart($product->id, $optionIds);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart',
            ]);
        } catch (Exception $e) {
            Log::error('Cart destroy error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to remove product from cart',
            ], 500);
        }
    }

    /**
     * Checkout the cart. Creates orders (grouped by vendor) and Stripe session.
     *
     * Query params:
     *  - vendor_id (optional) : only checkout items from specific vendor
     *  - use_api (optional) : if true, tries to call external Order API (keeps parity with your web controller)
     *
     * Returns JSON with Stripe session url on success.
     */
}
