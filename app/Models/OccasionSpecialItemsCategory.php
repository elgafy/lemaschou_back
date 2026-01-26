<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccasionSpecialItemsCategory extends Model
{
    protected $fillable=[
        'name_en','name_ar','order_before_period_in_hours'
    ];

    public function items () {
        return $this->hasMany(OccasionSpecialItems::class, 'category');
    }
}
