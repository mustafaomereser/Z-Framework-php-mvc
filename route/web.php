<?php

use zFramework\Core\Route;
use App\Controllers\HomeController;

Route::get('/test', function () {
    return view('test');
})->name('test');

Route::get('/language/{lang}', function ($lang) {
    zFramework\Core\Facades\Lang::locale($lang);
    back();
})->name('language');

Route::middleware([App\Middlewares\Auth::class])->group(function () {
    // middleware route example
});

// Route::get('/', [HomeController::class, 'index']);
// Route::get('/', 'HomeController@index')->name('index');

Route::resource('/', HomeController::class);
