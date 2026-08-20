<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GiftCard extends Model
{
    use HasFactory;

    protected $table = 'gift_cards';

    protected $fillable = [
        'title_en',
        'title_ar',
        'image',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('gift_cards'));
        static::deleted(fn () => Cache::forget('gift_cards'));
    }
}
