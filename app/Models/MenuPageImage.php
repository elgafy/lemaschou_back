<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MenuPageImage extends Model
{
    use HasFactory;

    protected $table='menu_page_images';

    protected $fillable=['image','image_ramadan'];

    protected function getImageAttribute($value)
    {
            //return asset('/storage' . '/' . $value);
            return Storage::disk('s3')->url($value);
    }

    protected function getImageRamadanAttribute($value)
    {
            //return asset('/storage' . '/' . $value);
            if($value!=null)
            {
                return Storage::disk('s3')->url($value);
            }else{
                return "";
            }

    }
}
