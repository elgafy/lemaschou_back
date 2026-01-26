<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialDays extends Model
{
    protected $fillable = [
        'date',
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'lunch_shift_payment_amount',
        'dinner_shift_payment_amount',
    ];

    public function orderItems() {
        return $this->morphMany(orderItems::class, 'itemable');
    }
}
