<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPage extends Model
{
    use HasFactory;

    protected $table='ad_pages';

    protected $fillable=['ad_id','page'];

    public function ad()
    {
        return $this->belongsTo(Ad::class,'ad_id');
    }
}
