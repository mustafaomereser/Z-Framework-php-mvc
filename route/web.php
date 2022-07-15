<?php

use App\Controllers\HomeController;
use zFramework\Core\Route;

// Route::get('/', 'HomeController@index')

Route::get('/', [HomeController::class, 'index']);

Route::get('/test', function () {
    return view('test');
})->name('test');

Route::get('/language/{lang}', function ($lang) {
    zFramework\Core\Facades\Lang::locale($lang);
    back();
});
