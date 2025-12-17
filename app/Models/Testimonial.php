<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;

    protected $table='testimonials';

    protected $fillable=['name_en','name_ar','image','status','desc_en','desc_ar','job_en','job_ar'];
    protected function getImageAttribute($value)
    {
            //return asset('/storage' . '/' . $value);
            return Storage::disk('s3')->url($value);
    }
}
