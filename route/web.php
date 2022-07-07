<?php

use App\Models\User;
use zFramework\Core\Route;

// Route::get('/', 'HomeController@index')

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    $user = new User;
    $user->insert([
        'username' => 'test',
        'password' => 'test',
        'email' => 'admin@localhost.com',
        'api_token' => 'test'
    ]);
});

Route::get('/language/{lang}', function ($lang) {
    zFramework\Core\Facades\Lang::locale($lang);
    back();
});
