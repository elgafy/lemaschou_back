<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/email', function () {
    return view('email');
});
Route::get('/send-test-email', function () {
    return view('emails.welcome', ['name' => 'John Doe']);
});
