<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $table='faqs';

    protected $fillable=['q_en','q_ar','a_en','a_ar','status','order'];
}
