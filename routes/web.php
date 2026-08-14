<?php

use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/email', function () {
    return view('email');
});
Route::get('/send-test-email', function () {
    $data = [
        'name' => 'John Doe'
    ];
    $name = 'Abdalaziz';

    // Target recipient and send the mailable instance
    // Mail::to('zizos79@gmail.com')->send(new WelcomeEmail($name));
    Mail::to('zizos79@gmail.com')->queue(new WelcomeEmail($name));

    return 'Email has been sent successfully!';
});
