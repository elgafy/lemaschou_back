<?php

namespace App\Http\Resources;

use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // public function getDesc($id,$lang)
    // {
    //    $item=Meal::where('id',$id)->first();
    //    if($lang=="en"){
    //        if($item->description_en!=null){
    //            $desc=$item->description_en;
    //        }else{
    //            $desc="";
    //        }
    //    }else{
    //        if($item->description_ar!=null){
    //            $desc=$item->description_ar;
    //        }else{
    //            $desc="";
    //        }
    //    }
    //    return $desc;
    // }
    public function toArray(Request $request): array
    {
        $lang = getLang();
        return [
            'id' => $this->id,
            //'name' => $lang == "en" ? $this->name_en : $this->name_ar,
            //'desc'=>$this->getDesc($this->id,$lang),
            // 'desc' => $lang == "en" ? $this->description_en : $this->description_ar,

            'name_en' => $this->name_en != null ? $this->name_en : "",
            'name_ar' => $this->name_ar != null ? $this->name_ar : "",
            'desc_en' => $this->description_en != null ? $this->description_en : "",
            'desc_ar' => $this->description_ar != null ? $this->description_ar : "",
            'image' => $this->image,
            'price' => $this->price,
            'calories' => $this->calories,
            'grams' => $this->grams ?? 0,
            'category_id' => $this->category_id,
            // 'category_name' => $lang == "en" ? $this->category->name_en : $this->category->name_ar,
            'category_name_en' => $this->category->name_en,
            'category_name_ar' => $this->category->name_ar,
            'featured_type' => $this->featured_type != null ? $this->featured_type : "",
            'is_ramadan'=>$this->is_ramadan=="1"?true:false,
        ];
    }
}
