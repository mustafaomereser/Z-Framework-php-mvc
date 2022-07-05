<?php

use App\Models\User;
use zFramework\Core\Route;

// Route::get('/', 'HomeController@index');

Route::get('/test', function () {
    $user = new User;
    print_r($user->get());
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/language/{lang}', function ($lang) {
    zFramework\Core\Facades\Lang::locale($lang);
    back();
});
