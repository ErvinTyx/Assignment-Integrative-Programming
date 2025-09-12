<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatusEnum;
use App\States\{DraftState, PaidState, ShippedState, DeliveredState, CancelledState, OrderStateInterface};
use Exception;

class Order extends Model
{
    protected $fillable = [
        'stripe_session_id',
        'user_id',
        'total_price',
        'status',
        'vendor_user_id',
        'online_payment_commission',
        'website_commission',
        'vendor_subtotal',
        'payment_intent',
    ];

    public function scopeForVendor(Builder $query): Builder
    {
        return $query->where('vendor_user_id', auth()->user()->id);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendorUser()
    {
        return $this->belongsTo(User::class, 'vendor_user_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_user_id', 'user_id');
    }

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];


    public function getState(): OrderStateInterface
    {
        return match ($this->status) {
            OrderStatusEnum::Draft => new DraftState(),
            OrderStatusEnum::Paid => new PaidState(),
            OrderStatusEnum::Shipped => new ShippedState(),
            OrderStatusEnum::Delivered => new DeliveredState(),
            OrderStatusEnum::Cancelled => new CancelledState(),
            default => throw new Exception("Unknown order status."),
        };
    }


    public function proceed(): void
    {
        $this->getState()->proceed($this);
    }


    public function cancel(): void
    {
        $this->getState()->cancel($this);
    }
}
