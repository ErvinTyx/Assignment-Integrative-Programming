<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Http\Resources\OrderViewResource;
use App\Mail\CheckoutCompleted;
use App\Mail\NewOrderMail;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Transfer;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function success(Request $request)
    {
        $user = auth()->user();
        $session_id = $request->get("session_id");
        $orders = Order::where("stripe_session_id", $session_id)->get();

        if ($orders->count() == 0) {
            abort(404);
        }

        foreach ($orders as $order) {
            if ($order->user_id !== $user->id) {
                abort(403);
            }
        }

        return Inertia::render('Stripe/Success', [
            'orders' => OrderViewResource::collection($orders)->collection->toArray(),
        ]);
    }

    public function failure(Request $request)
    {
        $user = auth()->user();
        $session_id = $request->get("session_id");

        $orders = collect(); // empty collection as fallback

        if ($session_id) {
            $orders = Order::with(['orderItems', 'vendorUser'])
                ->where("stripe_session_id", $session_id)
                ->get();

            foreach ($orders as $order) {
                if ($order->user_id === $user->id) {
                    $order->status = OrderStatusEnum::Failed; // mark as failed
                    $order->save();
                }
            }
        }

        return Inertia::render('Stripe/Failure', [
            'orders' => OrderViewResource::collection($orders)->collection->toArray(),
        ]);
    }



    public function webhook(Request $request)
    {
        $stripe = new StripeClient(config('app.stripe_secret_key'));

        $endpoint_secret = config('app.stripe_webhook_secret');

        $payload = $request->getContent();
        $sig_header = request()->header('stripe-signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error($e);
            return response('Invalid Payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error($e);
            return response('Invalid Payload', 400);
        }



        switch ($event->type) {
            case 'charge.updated':
                $charge = $event->data->object;
                $transactionId = $charge['balance_transaction'];
                $paymentIntent = $charge['payment_intent'];
                $balanceTransaction = $stripe->balanceTransactions->retrieve($transactionId);

                $orders = Order::where('payment_intent', $paymentIntent)->get();

                $totalAmount = $balanceTransaction['amount'];
                $stripeFee = 0;

                foreach ($balanceTransaction['fee_details'] as $fee_detail) {
                    if ($fee_detail['type'] === 'stripe_fee') {
                        $stripeFee = $fee_detail['amount'];
                    }
                }
                $plateformFreePercent = config('app.platform_fee_pct');

                foreach ($orders as $order) {
                    $vendorShare = $order->total_price / $totalAmount;

                    $order->online_payment_commission = $vendorShare * $stripeFee;
                    $order->website_commission = ($order->total_price - $order->online_payment_commission) / 100 * $plateformFreePercent;
                    $order->vendor_subtotal = $order->total_price - $order->online_payment_commission - $order->website_commission;

                    $order->save();

                    Mail::to($order->vendorUser)->send(new NewOrderMail($order));
                }

                Mail::to($orders[0]->user)->send(new CheckoutCompleted($orders));

            case 'checkout.session.completed':
                $session = $event->data->object;
                $pi = $session['payment_intent'];

                $orders = Order::query()
                    ->with(['orderItems'])
                    ->where(['stripe_session_id' => $session['id']])
                    ->get();

                $productToDeletedFromCart = [];

                foreach ($orders as $order) {
                    $order->payment_intent = $pi;
                    $order->status = OrderStatusEnum::Paid;
                    $order->save();

                    $productToDeletedFromCart = [
                        ...$productToDeletedFromCart,
                        ...$order->orderItems->map(fn($item) => $item->product_id)->toArray()
                    ];

                    // Reduce product quantity
                    foreach ($order->orderItems as $orderItem) {
                        $options = $orderItem->variation_type_option_ids;
                        $product = $orderItem->product;

                        if ($options) {
                            $normalizedOptions = json_encode(array_values($options));
                            $variation = $product->variations()
                                ->where('variation_type_option_ids', $normalizedOptions)
                                ->first();

                            if ($variation && $variation->quantity != null) {
                                $variation->quantity -= $orderItem->quantity;
                                $variation->save();
                            }
                        } else if ($product->quantity != null) {
                            $product->quantity -= $orderItem->quantity;
                            $product->save();
                        }
                    }

                    // Remove items from cart (internal/external API pattern)
                    $useApi = $request->query('use_api', false);

                    if ($useApi) {
                        // Call external API
                        $response = Http::timeout(10)->post(route('api.cart.remove-purchased'), [
                            'user_id' => $order->user_id,
                            'product_ids' => $productToDeletedFromCart,
                        ]);

                        if ($response->failed()) {
                            \Log::error('Failed to remove purchased items from cart via API');
                        }
                    } else {
                        // Internal DB deletion
                        CartItem::where('user_id', $order->user_id)
                            ->whereIn('product_id', $productToDeletedFromCart)
                            ->where('saved_for_later', false)
                            ->delete();
                    }
                }
                
                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }
        return response('', 200);
    }


    /**
     * Create or redirect vendor to Express Connect onboarding
     */
    public function connect(): RedirectResponse
    {
        $vendor = auth()->user()->vendor;

        Stripe::setApiKey(config('app.stripe_secret'));

        if (!$vendor->stripe_account_id) {
            $account = Account::create([
                'type' => 'express',
                'country' => 'MY',
                'email' => auth()->user()->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);

            $vendor->update([
                'stripe_account_id' => $account->id,
            ]);
        }

        $accountId = $vendor->stripe_account_id;

        $accountLink = AccountLink::create([
            'account' => $accountId,
            'refresh_url' => route('stripe.onboarding.refresh'),
            'return_url' => route('stripe.onboarding.return'),
            'type' => 'account_onboarding',
        ]);

        return redirect($accountLink->url);
    }

    /**
     * Handle return from onboarding
     */
    public function onboardingReturn()
    {
        $vendor = auth()->user()->vendor;

        Stripe::setApiKey(config('app.stripe_secret'));
        $account = Account::retrieve($vendor->stripe_account_id);

        if ($account->details_submitted) {
            $vendor->update(['stripe_account_active' => true]);
        }

        return redirect()->route('profile.edit')->with('success', 'Stripe onboarding completed!');
    }

    /**
     * Example: payout to vendor account
     */
    public function payout($vendorId)
    {
        $vendor = \App\Models\Vendor::findOrFail($vendorId);

        Stripe::setApiKey(config('app.stripe_secret'));

        $transfer = Transfer::create([
            'amount' => 10000, // RM100.00 (amount in cents)
            'currency' => 'myr',
            'destination' => $vendor->stripe_account_id,
        ]);

        return response()->json($transfer);
    }

}
