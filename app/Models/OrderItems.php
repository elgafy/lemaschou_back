<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    protected $fillable = [
        'order_id',
        'itemable_id',
        'itemable_type',
        'name',
        'name_en',
        'name_ar',
        'variation',
        'variation_en',
        'variation_ar',
        'category',
        'quantity',
        'unit_price',
        'sub_total',
        'vat',
        'total',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}
