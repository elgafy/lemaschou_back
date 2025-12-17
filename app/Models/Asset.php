<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Asset extends Model
{
    use HasFactory;

    protected $table='assets';

    protected $fillable=['image','type'];

    protected function getImageAttribute($value)
    {
         return Storage::disk('s3')->url($value);
    }
}
