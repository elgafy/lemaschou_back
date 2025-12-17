<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ad extends Model
{
    use HasFactory;

    protected $table = 'ads';

    protected $fillable = ['image','image_mobile','show_one_time', 'link'];

    protected function getImageAttribute($value)
    {
        //return asset('/storage' . '/' . $value);
        return Storage::disk('s3')->url($value);
    }

      protected function getImageMobileAttribute($value)
    {
        //return asset('/storage' . '/' . $value);
        return Storage::disk('s3')->url($value);
    }

    public function adPages()
    {
        return $this->hasMany(AdPage::class, 'ad_id');
    }
}
