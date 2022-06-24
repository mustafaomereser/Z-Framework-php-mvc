<?php

use App\Controllers\ExamplesController;
use Core\Facedas\Lang;
use Core\Route;
use Core\View;

Route::any('/', function () {
    return View::view('welcome');
});

Route::get('/language/{lang}', function ($lang) {
    Lang::locale($lang);
    back();
});

Route::resource('/examples', ExamplesController::class);