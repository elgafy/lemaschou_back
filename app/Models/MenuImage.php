<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MenuImage extends Model
{
    use HasFactory;

    protected $table='menu_images';

    protected $fillable=['image','text_en','text_ar'];

    protected function getImageAttribute($value)
    {
            //return asset('/storage' . '/' . $value);
            return Storage::disk('s3')->url($value);
    }
}
