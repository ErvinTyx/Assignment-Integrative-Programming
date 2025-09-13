<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'variation_type_option_ids'
    ];

    protected $casts = [
        'variation_type_option_ids' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getVariationOptionsAttribute()
    {
        $ids = $this->variation_type_option_ids;
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        return \App\Models\VariationTypeOption::whereIn('id', $ids)->get();
    }
}
