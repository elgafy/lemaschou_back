<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    use HasFactory;

    protected $table='galleries';

    protected $fillable=['image','status'];

    protected function getImageAttribute($value)
    {
            //return asset('/storage' . '/' . $value);
            return Storage::disk('s3')->url($value);
    }
}
