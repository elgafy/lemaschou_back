<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    //
    protected static function booted()
    {
        static::creating(function ($model) {
            // Automatically generate a unique 8-character string
            $model->reservation_id = self::generateUniqueHash();
        });
    }

    /**
     * Generate a truly unique 8-character hash ID.
     */
    protected static function generateUniqueHash(): string
    {
        do {
            // Generates a random alphanumeric string (letters and numbers)
            $hash = Str::random(8);
        } while (self::where('reservation_id', $hash)->exists()); // Prevents collisions

        return $hash;
    }


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
