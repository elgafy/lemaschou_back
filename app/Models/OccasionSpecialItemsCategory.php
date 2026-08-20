<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class OccasionSpecialItemsCategory extends Model
{
    protected $fillable = [
        'name_en', 'name_ar', 'order_before_period_in_hours',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('occasion_items'));
        static::deleted(fn () => Cache::forget('occasion_items'));
    }

    public function items()
    {
        return $this->hasMany(OccasionSpecialItems::class, 'category');
    }
}
