<?php

use App\Controllers\ExamplesController;
use zFramework\Core\Facades\Lang;
use zFramework\Core\Route;
use zFramework\Core\View;

Route::any('/', function () {
    return View::view('welcome');
});

Route::get('/language/{lang}', function ($lang) {
    Lang::locale($lang);
    back();
});

Route::resource('/examples', ExamplesController::class);

Route::any('/test-test/{id}', function ($id) {
    echo "test $id";
});

Route::pre('/admin')->group(function () {
    Route::any('/', function () {
        echo 'naber';
    });
});
