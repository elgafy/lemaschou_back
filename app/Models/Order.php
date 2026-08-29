<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'reservation_id',
        'subtotal',
        'discount',
        'deposit',
        'total',
        'payment_processor',
        'currency',
        'status',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItems::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
