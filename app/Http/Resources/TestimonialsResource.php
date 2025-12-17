<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang=getLang();
        return[
           'id'=>$this->id,
           'name'=>$lang=="en"?$this->name_en:$this->name_ar,
           'desc'=>$lang=="en"?$this->desc_en:$this->desc_ar,
           'job'=>$lang=="en"?$this->job_en:$this->job_ar,
           'image'=>$this->image
        ];
    }
}
