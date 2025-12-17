<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Meal extends Model
{
    use HasFactory;

    protected $table='meals';

    protected $fillable=[
        'name_en','name_ar','description_en','description_ar',
        'calories','grams','price','image','status',
        'category_id','featured_type','order','is_ramadan','is_menu'
    ];

    // protected function getImageAttribute($value)
    // {
    //         return asset('/storage' . '/' . $value);
    // }
    protected function getImageAttribute($value)
    {
            //return asset('/storage' . '/' . $value);
            if($value!=null)
            {
                return Storage::disk('s3')->url($value);
            }else{
                return "";
            }

    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    protected $casts = [
        'status' => 'boolean',
    ];
}
