<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::truncate();
        Setting::updateOrCreate(['key' => 'about_en'], ['value' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industries standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.']);
        Setting::updateOrCreate(['key' => 'about_ar'], ['value' => 'لوريم إيبسوم(Lorem Ipsum) هو ببساطة نص شكلي (بمعنى أن الغاية هي الشكل وليس المحتوى) ويُستخدم في صناعات المطابع ودور النشر. كان لوريم إيبسوم ولايزال المعيار للنص الشكلي منذ القرن الخامس عشر عندما قامت مطبعة مجهولة برص مجموعة من الأحرف بشكل عشوائي أخذتها من نص، لتكوّن كتيّب بمثابة دليل أو مرجع شكلي لهذه الأحرف. خمسة قرون من الزمن لم تقضي على هذا النص، بل انه حتى صار مستخدماً وبشكله الأصلي في الطباعة والتنضيد الإلكتروني. انتشر بشكل كبير في ستينيّات هذا القرن مع إصدار رقائق "ليتراسيت" (Letraset) البلاستيكية تحوي مقاطع من هذا النص، وعاد لينتشر مرة أخرى مؤخراَ مع ظهور برامج النشر الإلكتروني مثل "ألدوس بايج مايكر" (Aldus PageMaker) والتي حوت أيضاً على نسخ من نص لوريم إيبسوم.']);
        Setting::updateOrCreate(['key' => 'email'], ['value' => 'info@LeMaschou.com']);
        Setting::updateOrCreate(['key' => 'phone'], ['value' => '+20585232555']);
        Setting::updateOrCreate(['key' => 'address_en'],['value'=>'1959 Sepulveda Blvd.Culver City, CA, 90230']);
        Setting::updateOrCreate(['key' => 'address_ar'],['value'=>'1959 شارع سيبولفيدا، كولفر سيتي، كاليفورنيا، 90230']);
        // Setting::updateOrCreate(['key' => 'facebook'], ['value' => 'facebook@LeMaschou.com']);
        Setting::updateOrCreate(['key' => 'instagram'], ['value' => 'instagram@LeMaschou.com']);
        // Setting::updateOrCreate(['key' => 'twitter'], ['value' => 'https://x.com/lemaschou_sa?lang=en']);
        // Setting::updateOrCreate(['key' => 'youtube'], ['value' => 'https://www.youtube.com/@lemaschou249']);
        Setting::updateOrCreate(['key' => 'from'], ['value' => '12:00']);
        Setting::updateOrCreate(['key' => 'to'], ['value' => '23:00']);
        Setting::updateOrCreate(['key' => 'reservation_link'], ['value' => 'https://www.sevenrooms.com/reservations/lemaschou']);
        Setting::updateOrCreate(['key' => 'active_ramadan_menu'], ['value' => '0']);

    }
}
