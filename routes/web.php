<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/{signIn}', function ($auth) {
    return view(view: 'signIn', data: ['signIn' => $auth]);
})->where('signIn', '.*');