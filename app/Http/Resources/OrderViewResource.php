<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\States\{DraftState, PaidState, ShippedState, DeliveredState, CancelledState, OrderStateInterface};

class OrderViewResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $state = $this->getState(); // getState() comes from the Order model

        $canProceed = true;
        $canCancel = true;
        $nextStatus = null;

        try {
            $state->proceed(clone $this); // use clone to avoid saving
            $nextStatus = $this->status->value;
        } catch (\Throwable $e) {
            $canProceed = false;
        }

        try {
            $state->cancel(clone $this);
        } catch (\Throwable $e) {
            $canCancel = false;
        }

        return [
            "id" => $this->id,
            "total_price" => $this->total_price,
            "status" => $this->status,
            "created_at" => $this->created_at->format("Y-m-d H:i:s"),
            "next_possible_status" => $nextStatus,
            "can_proceed" => $canProceed,
            "can_cancel" => $canCancel,
            "vendorUser" => new VendorUserResource($this->vendorUser),
            "orderItems" => $this->orderItems->map(fn($item) => [
                "id" => $item->id,
                "quantity" => $item->quantity,
                "price" => $item->total_price,
                "variation_type_option_ids" => $item->variation_type_option_ids,
                "product" => [
                    "id" => $item->product->id,
                    "title" => $item->product->title,
                    "slug" => $item->product->slug,
                    "description" => $item->product->description,
                    "image" => $item->product->getImageForOptions($item->variation_type_option_ids ?: []),
                ],
            ]),
        ];
    }
}


