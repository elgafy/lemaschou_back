<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = getLang();
        return [
            'id' => $this->id,
            'question' => $lang == "en" ? $this->q_en : $this->q_ar,
            'answer' => $lang == "en" ? $this->a_en : $this->a_ar,
        ];
    }
}
