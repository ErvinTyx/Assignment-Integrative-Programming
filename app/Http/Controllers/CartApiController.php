<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartApiController extends Controller
{
    /**
     * List all cart items for a user
     * GET /api/cart
     */
    public function index(Request $request)
    {
        $userId = $request->query('user_id') ?? Auth::id();

        $cartItems = CartItem::with('product')
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems,
        ]);
    }

    /**
     * Add item to cart
     * POST /api/cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'option_ids' => 'nullable|array',
            'saved_for_later' => 'nullable|boolean',
        ]);

        $cartItem = CartItem::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'variation_type_option_ids' => $request->option_ids ?? [],
            ],
            [
                'quantity' => $request->quantity,
                'saved_for_later' => $request->saved_for_later ?? false,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $cartItem,
        ], 201);
    }

    /**
     * Update quantity or saved_for_later status
     * PUT /api/cart/{cartItem}
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'sometimes|integer|min:1',
            'saved_for_later' => 'sometimes|boolean',
        ]);

        $cartItem->update($request->only(['quantity', 'saved_for_later']));

        return response()->json([
            'success' => true,
            'data' => $cartItem,
        ]);
    }

    /**
     * Remove an item from cart
     * DELETE /api/cart/{cartItem}
     */
    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item removed',
        ]);
    }

    /**
     * Remove purchased products from cart
     * POST /api/cart/remove-purchased
     */
    public function removePurchased(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'product_ids' => 'required|array',
        ]);

        CartItem::where('user_id', $request->user_id)
            ->whereIn('product_id', $request->product_ids)
            ->where('saved_for_later', false)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchased products removed from cart.'
        ]);
    }
}
