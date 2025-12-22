<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FooterResource;
use App\Models\Ad;
use App\Models\AdPage;
use App\Models\Asset;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function getFooter()
    {
        $lang = getLang();
        $result = [
            'phone' => Setting::where('key', 'phone')->first()->value ?? '',
            'email' => Setting::where('key', 'email')->first()->value ?? '',
            'address' => $lang == "en" ? Setting::where('key', 'address_en')->first()->value : Setting::where('key', 'address_ar')->first()->value,
            // 'facebook'=>Setting::where('key', 'facebook')->first()->value,
            'instagram' => Setting::where('key', 'instagram')->first()->value ?? '',
            // 'twitter'=>Setting::where('key', 'twitter')->first()->value,
            // 'youtube'=>Setting::where('key', 'youtube')->first()->value,
            'from' => Carbon::parse(Setting::where('key', 'from')->first()->value)->format('h:i A') ?? '',
            'to' => Carbon::parse(Setting::where('key', 'to')->first()->value)->format('h:i A') ?? '',
        ];
        return response()->res(success(), 'footer',  $result, 200);
    }

    public function getAd()
    {
        $ad = Ad::first();
        if($ad==null)
        {
            $result = [
                'desktop_image' => "",
                'mobile_image' => "",
                'show_one_time' => "",
                'link'=> "",
                'ad_pages'=>array()
            ];
            return response()->res(success(), 'ad',  $result, 200);
        }else{
            $result = [
                'desktop_image' => $ad->image ?? '',
                'mobile_image' => $ad->image_mobile ?? '',
                'show_one_time' => $ad->show_one_time=="1"?true:false,
                'link'=> $ad->link ?? '',
                'ad_pages'=>AdPage::where('ad_id',$ad->id)->select('page')->get()
            ];
            return response()->res(success(), 'ad',  $result, 200);
        }

    }

    public function getAssets(){
        $result=Asset::select('image','type')->get();
        return response()->res(success(), 'assets',  $result, 200);
    }

    public function getReservationSettings() {
        $result = [
            'use_external_reservation_link' => Setting::where('key', 'use_reservation_external_link')->first()->value ?? '',
            'external_reservation_link' => Setting::where('key', 'reservation_link')->first()->value ?? '',
        ];
        return response()->json([
            'success' => true, // based on the success() function name in your code
            'data' => $result,
        ], 200);
    }
}
