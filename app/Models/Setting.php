<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    protected $table = 'settings';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('reservation_settings'));
        static::deleted(fn () => Cache::forget('reservation_settings'));
    }
}
