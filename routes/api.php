<?php

use App\Http\Controllers\Api\ContactsController;
use App\Http\Controllers\Api\PagesController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('settings')->group(function () {
    Route::get('footer', [SettingsController::class, 'getFooter']);
    Route::get('ad', [SettingsController::class, 'getAd']);
    Route::get('assets', [SettingsController::class, 'getAssets']);
});

Route::prefix('pages')->group(function () {
    Route::get('home', [PagesController::class, 'home']);
    Route::get('menu', [PagesController::class, 'menu']);
    Route::get('meal-details/{meal_id}', [PagesController::class, 'mealDetails']);
    Route::get('venue', [PagesController::class, 'venue']);
    Route::get('faqs', [PagesController::class, 'faqs']);
});

Route::post('contact/send', [ContactsController::class, 'sendContact']);

Route::get('menu/request', [PagesController::class, 'menuRequest']);
