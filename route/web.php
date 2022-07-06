<?php

use App\Models\User;
use zFramework\Core\Facades\Str;
use zFramework\Core\Route;

// Route::get('/', 'HomeController@index');

Route::get('/test', function () {
    $user = new User;

    $user->insert([
        'username' => 'test2ttt',
        'password' => 'testtttt',
        'email' => Str::rand('5') . '@' . Str::rand('4') . '.com',
        'api_token' => 'testttt',
    ]);

    exit;
    return view('test');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/language/{lang}', function ($lang) {
    zFramework\Core\Facades\Lang::locale($lang);
    back();
});
