<?php

use App\Http\Controllers\Api\ContactsController;
use App\Http\Controllers\Api\PagesController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('settings')->group(function () {
    // Route::get('footer', [SettingsController::class, 'getFooter']);
    // Route::get('ad', [SettingsController::class, 'getAd']);
    Route::get('assets', [SettingsController::class, 'getAssets']);
    Route::get('all', [SettingsController::class, 'getSettings']);
});
Route::prefix('reservations')->group(function () {
    Route::get('settings', [ReservationController::class, 'getReservationSettings']);
    Route::get('venues', [ReservationController::class, 'getVenues']);
    Route::get('check-availability/{date}/{guests?}/{starttime?}/{endtime?}/',[ReservationController::class, 'checkAvailability']);
    Route::get('occasion-items',[ReservationController::class, 'getOccasionItems']);
    Route::post('book',[ReservationController::class, 'book']);
});

Route::prefix('pages')->group(function () {
    Route::get('home', [PagesController::class, 'home']);
    Route::get('menu', [PagesController::class, 'menu']);
    Route::get('meal-details/{meal_id}', [PagesController::class, 'mealDetails']);
    Route::get('venue', [PagesController::class, 'venue']);
    Route::get('faqs', [PagesController::class, 'faqs']);
    Route::get('terms', [PagesController::class, 'terms']);
    Route::get('payment-policy', [PagesController::class, 'paymentPolicy']);
    Route::get('reservation', [PagesController::class, 'reservation']);
});

Route::post('contact/send', [ContactsController::class, 'sendContact']);

Route::get('menu/request', [PagesController::class, 'menuRequest']);
