<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasFactory;

    protected $table='videos';

    protected $fillable=['video'];

    protected function getVideoAttribute($value)
    {
            //return asset('/storage' . '/' . $value);
            return Storage::disk('s3')->url($value);
    }
}
