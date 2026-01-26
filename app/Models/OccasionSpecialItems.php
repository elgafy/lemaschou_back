<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccasionSpecialItems extends Model
{
    protected $fillable=[
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'price',
        'reservation_availability_period',
        'category',
        'image',
        // 'enable_card'
    ];

    public function category() {
        return $this->hasOne(OccasionSpecialItemsCategory::class, 'category');
    }

    public function orderItems() {
        return $this->morphMany(orderItems::class, 'itemable');
    }
}
