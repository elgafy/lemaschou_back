<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    //
    protected $fillable = [
        'status',
        'date',
        'time',
        'guests_count',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'special_request',
        'occasion',
        'occasion_type',
        'occasion_items',
        'allergic',
        'food_allergies',
        'terms_accepted',
        'deposite',
        'payment_terms_accepted',
        'options',
    ];

    protected $casts = [
        'occasion_items' => 'array',
        'food_allergies' => 'array',
        'options' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
