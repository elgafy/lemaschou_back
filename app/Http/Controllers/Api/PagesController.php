<?php

namespace App\Http\Controllers\Api;

use App\Filament\Resources\TestimonialResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\EventsResource;
use App\Http\Resources\FaqsResource;
use App\Http\Resources\MealDetailsResource;
use App\Http\Resources\MealsResource;
use App\Http\Resources\TestimonialsResource;
use App\Models\Category;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Image;
use App\Models\Meal;
use App\Models\MenuImage;
use App\Models\MenuPageImage;
use App\Models\Seo;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Venue;
use App\Models\Video;
use Illuminate\Http\Request;
use IntlDateFormatter;
use DateTime;

use Symfony\Component\Console\Output\ConsoleOutput;

class PagesController extends Controller
{
    public function home()
    {
        $lang = getLang();
        $reservationLink = Setting::where('key', 'use_reservation_external_link')->first()->value ? (Setting::where('key', 'reservation_link')->first()->value ?? $lang . '/reservation') : $lang . '/reservation';
        if ($lang == "en") {
            $homeDescSeo = Seo::where('key', 'desc_home_en')->first()->value ?? '';
            $homeKeywordsSeo = Seo::where('key', 'keywords_home_en')->first()->value ?? '';
            $menuText = MenuImage::first()->text_en ?? '';
        } else {
            $homeDescSeo = Seo::where('key', 'desc_home_ar')->first()->value ?? '';
            $homeKeywordsSeo = Seo::where('key', 'keywords_home_ar')->first()->value ?? '';
            $menuText = MenuImage::first()->text_ar ?? '';
        }
        $menuImage = MenuImage::first()->image ?? '';
        $video = Video::first()->video ?? '';
        // $reservationLink = Setting::where('key', 'reservation_link')->first()->value ?? '';

        $venues = Venue::where('status', '1')->select('id', 'image', 'is_main')->take(3)->latest()->get();
        $testimonials = Testimonial::where('status', '1')->take(3)->get();
        $faqs = Faq::where('status', '1')->orderBy('order')->get();

        $data = [
            'desc_home_seo' => $homeDescSeo,
            'keywords_home_seo' => $homeKeywordsSeo,
            'reservation_link' => $reservationLink,
            'menu_image' => $menuImage,
            'menu_text' => $menuText,
            'video' => $video,
            'venues' => $venues,
            'testimonials' => TestimonialsResource::collection($testimonials),
            'faqs' => FaqsResource::collection($faqs)
        ];
        return response()->res(success(), 'home_page',  $data, 200);
    }

    public function menu()
    {
        $lang = getLang();
        $activeMenuRamadan = Setting::where('key', 'active_ramadan_menu')->first()->value ?? '';

        if ($lang == "en") {
            $menuDescSeo = Seo::where('key', 'desc_menu_en')->first()->value ?? '';
            $menuKeywordsSeo = Seo::where('key', 'keywords_menu_en')->first()->value ?? '';
        } else {
            $menuDescSeo = Seo::where('key', 'desc_menu_ar')->first()->value ?? '';
            $menuKeywordsSeo = Seo::where('key', 'keywords_menu_ar')->first()->value ?? '';
        }

        if ($activeMenuRamadan == '0') {
            $menuImage = MenuPageImage::first()->image ?? '';
        } else {
            $menuImage = MenuPageImage::first()->image_ramadan ?? '';
        }

        if ($activeMenuRamadan == '0') {
            $defaultMeals = Meal::inRandomOrder()->limit(4)->get();
        } else {
            // echo "Today is NOT in Ramadan.";
            $defaultMeals = [];
        }
        $data = [
            'active_ramadan_menu' => $activeMenuRamadan,
            'desc_menu_seo' => $menuDescSeo,
            'keywords_menu_seo' => $menuKeywordsSeo,
            'image' => $menuImage,
            'default_meals' => MealsResource::collection($defaultMeals),
        ];
        return response()->res(success(), 'menu_page',  $data, 200);
    }

    public function mealDetails($meal_id)
    {
        $meal = Meal::where('id', $meal_id)->get();
        if ($meal->count() == 0) {
            return response()->res(failed(), 'meal_not_found',  [], 404);
        } else {
            return response()->res(success(), 'meal_details',  MealDetailsResource::collection($meal), 200);
        }
    }

    public function venue()
    {
        $lang = getLang();
        if ($lang == "en") {
            $venueDescSeo = Seo::where('key', 'desc_venue_en')->first()->value ?? '';
            $venueKeywordsSeo = Seo::where('key', 'keywords_venue_en')->first()->value ?? '';
            $about = Setting::where('key', 'about_en')->first()->value ?? '';
        } else {
            $venueDescSeo = Seo::where('key', 'desc_venue_ar')->first()->value ?? '';
            $venueKeywordsSeo = Seo::where('key', 'keywords_venue_ar')->first()->value ?? '';
            $about = Setting::where('key', 'about_ar')->first()->value ?? '';
        }
        $gallery = Gallery::select('id', 'image')->take('7')->latest()->get();
        $events = Event::take('3')->latest()->get();

        $about_image = Image::first()->image ?? '';
        $data = [
            'desc_venue_seo' => $venueDescSeo,
            'keywords_venue_seo' => $venueKeywordsSeo,
            'about_image' => $about_image,
            'about' => $about,
            'gallery' => $gallery,
            'events' => EventsResource::collection($events),
        ];
        return response()->res(success(), 'venue_page',  $data, 200);
    }

    public function menuRequest()
    {
        $output = new ConsoleOutput();
        $output->writeln("Start menu request from controller");
        $activeMenuRamadan = Setting::where('key', 'active_ramadan_menu')->first()->value ?? '';
        $output->writeln("Get ramadan settings");

        if ($activeMenuRamadan == '1') {
            // echo "Today is in Ramadan!";
            $categories = Category::where('status', '1')->where('is_ramadan', '1')->where('is_menu', '1')->orderBy('order')->get();
            $getCategories = Category::where('status', '1')->where('is_ramadan', '1')->where('is_menu', '1')->orderBy('order')->pluck('id');
            $meals = Meal::whereIn('category_id', $getCategories)
                ->where('status', '1')->where('is_ramadan', '1')->where('is_menu', '1')->orderBy('order')->get();
            $data = [
                'categories' => CategoriesResource::collection($categories),
                'meals' => MealsResource::collection($meals)
            ];
            return response()->res(success(), 'menu_reuqest',  $data, 200);
        } else {
            // echo "Today is NOT in Ramadan.";
            $categories = Category::where('status', '1')->where('is_ramadan', '0')->where('is_menu', '1')->orderBy('order')->get();
            $getCategories = Category::where('status', '1')->where('is_ramadan', '0')->where('is_menu', '1')->orderBy('order')->pluck('id');
            $meals = Meal::whereIn('category_id', $getCategories)
            ->where('status', '1')->where('is_ramadan', '0')->where('is_menu', '1')->orderBy('order')->get();
            // $data = [
            //     'categories' => CategoriesResource::collection($categories),
            //     'meals' => MealsResource::collection($meals)
            // ];
            // return response()->res(success(), 'menu_reuqest',  $data, 200);
            // return response()->json([
            //     'success' => true, // based on the success() function name in your code
            //     'data' => ['categories' => $categories, 'meals' => $meals],
            // ], 200);
            $data = [
                'categories' => CategoriesResource::collection($categories),
                'meals' => MealsResource::collection($meals)
            ];
            return response()->res(success(), 'menu_reuqest',  $data, 200);
        }
    }

    public function faqs()
    {
        $lang = getLang();
        if ($lang == "en") {
            $faqDescSeo = Seo::where('key', 'desc_faq_en')->first()->value ?? '';
            $faqKeywordsSeo = Seo::where('key', 'keywords_faq_en')->first()->value ?? '';
        } else {
            $faqDescSeo = Seo::where('key', 'desc_faq_ar')->first()->value ?? '';
            $faqKeywordsSeo = Seo::where('key', 'keywords_faq_ar')->first()->value ?? '';
        }
        $faqs = Faq::where('status', '1')->orderBy('order')->get();
        $data = [
            'desc_faq_seo' => $faqDescSeo,
            'keywords_faq_seo' => $faqKeywordsSeo,
            'faqs' => FaqsResource::collection($faqs)
        ];
        return response()->res(success(), 'faq_page',  $data, 200);

    }

    public function terms()
    {
        $lang = getLang();
        if ($lang == "en") {
            $termsDescSeo = Seo::where('key', 'desc_terms_en')->first()->value ?? '';
            $termsKeywordsSeo = Seo::where('key', 'keywords_terms_en')->first()->value ?? '';
            $termsTitle = Setting::where('key', 'privacy_terms_page_title_en')->first()->value ?? '';
            $termsContent = Setting::where('key', 'privacy_terms_page_content_en')->first()->value ?? '';
        } else {
            $termsDescSeo = Seo::where('key', 'desc_terms_ar')->first()->value ?? '';
            $termsKeywordsSeo = Seo::where('key', 'keywords_terms_ar')->first()->value ?? '';
            $termsTitle = Setting::where('key', 'privacy_terms_page_title_ar')->first()->value ?? '';
            $termsContent = Setting::where('key', 'privacy_terms_page_content_ar')->first()->value ?? '';
        }
        $data = [
            'desc_terms_seo' => $termsDescSeo,
            'keywords_terms_seo' => $termsKeywordsSeo,
            'title' => $termsTitle,
            'content' => $termsContent
        ];
        $output = new ConsoleOutput();
        $output->writeln("Terms data: " . json_encode($data));
        return response()->res(success(), 'terms_page',  $data, 200);

    }
    public function paymentPolicy()
    {
        $lang = getLang();
        if ($lang == "en") {
            $payment_policyDescSeo = Seo::where('key', 'desc_payment_policy_en')->first()->value ?? '';
            $payment_policyKeywordsSeo = Seo::where('key', 'keywords_payment_policy_en')->first()->value ?? '';
            $payment_policyTitle = Setting::where('key', 'payment_terms_page_title_en')->first()->value ?? '';
            $payment_policyContent = Setting::where('key', 'payment_terms_page_content_en')->first()->value ?? '';
        } else {
            $payment_policyDescSeo = Seo::where('key', 'desc_payment_policy_ar')->first()->value ?? '';
            $payment_policyKeywordsSeo = Seo::where('key', 'keywords_payment_policy_ar')->first()->value ?? '';
            $payment_policyTitle = Setting::where('key', 'payment_terms_page_title_ar')->first()->value ?? '';
            $payment_policyContent = Setting::where('key', 'payment_terms_page_content_ar')->first()->value ?? '';
        }
        $data = [
            'desc_payment_policy_seo' => $payment_policyDescSeo,
            'keywords_payment_policy_seo' => $payment_policyKeywordsSeo,
            'title' => $payment_policyTitle,
            'content' => $payment_policyContent
        ];
        $output = new ConsoleOutput();
        $output->writeln("payment_policy data: " . json_encode($data));
        return response()->res(success(), 'payment_policy_page',  $data, 200);

    }
    public function reservation()
    {
        $lang = getLang();
        if ($lang == "en") {
            $reservationDescSeo = Seo::where('key', 'desc_reservation_en')->first()->value ?? '';
            $reservationKeywordsSeo = Seo::where('key', 'keywords_reservation_en')->first()->value ?? '';
        } else {
            $reservationDescSeo = Seo::where('key', 'desc_reservation_ar')->first()->value ?? '';
            $reservationKeywordsSeo = Seo::where('key', 'keywords_reservation_ar')->first()->value ?? '';
        }
        $data = [
            'desc_reservation_seo' => $reservationDescSeo,
            'keywords_reservation_seo' => $reservationKeywordsSeo,
        ];
        return response()->res(success(), 'reservation_page',  $data, 200);

    }
}
