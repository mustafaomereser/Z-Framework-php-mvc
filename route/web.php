<?php

use App\Models\User;
use zFramework\Core\Route;

// Route::get('/', 'HomeController@index');

Route::get('/test', function () {
    $user = new User;
    $delete = $user->where('id', '=', 2)->delete();
    echo 'silindi: ' . ($delete ? $delete : 'yok');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/language/{lang}', function ($lang) {
    zFramework\Core\Facades\Lang::locale($lang);
    back();
});
