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
