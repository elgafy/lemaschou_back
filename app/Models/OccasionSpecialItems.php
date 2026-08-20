<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OccasionSpecialItems extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'price',
        'reservation_availability_period',
        'available_before_time',
        'category',
        'image',
        'has_variations',
        'variations',
        'options',
    ];

    protected $casts = [
        'variations' => 'array',
        'options' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('occasion_items'));
        static::deleted(fn () => Cache::forget('occasion_items'));
    }

    public function category()
    {
        return $this->hasOne(OccasionSpecialItemsCategory::class, 'category');
    }

    public function orderItems()
    {
        return $this->morphMany(orderItems::class, 'itemable');
    }
}
