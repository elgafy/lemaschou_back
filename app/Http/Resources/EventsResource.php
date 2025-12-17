<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventsResource extends JsonResource
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
            'image'=>$this->image,
         ];
    }
}
