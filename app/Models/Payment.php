<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'amount',
        'currency',
        'status',
        'gateway_order_id',
        'gateway_transaction_id',
        'gateway_session_id',
        'gateway_response',
        'paid_at',
        'payment_history',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'payment_history' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
