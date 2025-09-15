<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Transfer;

class StripeApiController extends Controller
{
    /**
     * Handle successful checkout
     */
    public function success(Request $request)
    {
        $sessionId = $request->get("session_id");

        if (!$sessionId) {
            return response()->json(['error' => 'Missing session_id'], 400);
        }

        $orders = Order::where("stripe_session_id", $sessionId)->get();

        if ($orders->isEmpty()) {
            return response()->json(['error' => 'Orders not found'], 404);
        }

        foreach ($orders as $order) {
            $order->status = OrderStatusEnum::Paid;
            $order->save();
        }

        return response()->json([
            'message' => 'Payment successful',
            'orders' => $orders
        ]);
    }

    /**
     * Handle cancelled checkout
     */
    public function failure(Request $request)
    {
        $sessionId = $request->get("session_id");

        if (!$sessionId) {
            return response()->json(['error' => 'Missing session_id'], 400);
        }

        $orders = Order::where("stripe_session_id", $sessionId)->get();

        foreach ($orders as $order) {
            $order->status = OrderStatusEnum::Failed;
            $order->save();
        }

        return response()->json([
            'message' => 'Payment cancelled',
            'orders' => $orders
        ]);
    }

    /**
     * Stripe Webhook
     */
    public function webhook(Request $request)
    {
        $stripe = new StripeClient(config('app.stripe_secret_key'));
        $endpoint_secret = config('app.stripe_webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('stripe-signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // You can reuse your existing switch-case from StripeController
        Log::info("Stripe webhook received: {$event->type}");

        return response()->json(['status' => 'success']);
    }

    /**
     * Stripe Connect Onboarding
     */
    public function connect(Request $request)
    {
        $vendor = $request->user()->vendor;

        Stripe::setApiKey(config('app.stripe_secret'));

        if (!$vendor->stripe_account_id) {
            $account = Account::create([
                'type' => 'express',
                'country' => 'MY',
                'email' => $request->user()->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);

            $vendor->update(['stripe_account_id' => $account->id]);
        }

        $accountLink = AccountLink::create([
            'account' => $vendor->stripe_account_id,
            'refresh_url' => config('app.url') . '/api/stripe/onboarding/refresh',
            'return_url'  => config('app.url') . '/api/stripe/onboarding/return',
            'type' => 'account_onboarding',
        ]);

        return response()->json(['url' => $accountLink->url]);
    }

    /**
     * Payout example
     */
    public function payout(Request $request, $vendorId)
    {
        $vendor = \App\Models\Vendor::findOrFail($vendorId);

        Stripe::setApiKey(config('app.stripe_secret'));

        $transfer = Transfer::create([
            'amount' => 10000, // RM100
            'currency' => 'myr',
            'destination' => $vendor->stripe_account_id,
        ]);

        return response()->json($transfer);
    }
}
