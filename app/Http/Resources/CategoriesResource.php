<?php

namespace App\Http\Resources;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = getLang();
        $search = ['-', '&', '!'];
        $replace = [' ', 'and', ''];

        return [
            'id' => $this->id,
            // 'name'=>$lang=="en"?$this->name_en:$this->name_ar,
            'grouped'=>$this->grouped == "0" ? false : true,
            'name_en' => str_replace($search, $replace,  $this->name_en),
            'name_ar' => str_replace($search, $replace, $this->name_ar),
            // 'grouped' => false,
            'is_ramadan' => $this->is_ramadan == "1" ? true : false,
            'title_en' => $this->title_en!=null ?$this->title_en:"",
            'title_ar' => $this->title_ar!=null ?$this->title_ar:"",
        ];
    }
}
