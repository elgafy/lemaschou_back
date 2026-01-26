<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        "user_id",
        "user_email",
        "payment_status",
        "price",
        "vat",
        "total",
        "payment_processor",
        "payment_reference",
    ];

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function items() {
    //     return $this->belongsToMany(OccasionSpecialItems::class, 'order_items', 'order_id', 'item_id')
    //     ->withPivot('quantity', 'price')
    //     ->withTimestamps();
    // }
    public function items()
    {
        return $this->hasMany(OrderItems::class);
    }
}
