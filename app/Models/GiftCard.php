<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use HasFactory;

    protected $table = 'gift_cards';

    protected $fillable = [
        'title_en',
        'title_ar',
        'image',
    ];
}
