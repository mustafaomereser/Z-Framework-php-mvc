<?php

use Modules\Blog\Controllers\Home\HomeController;
use zFramework\Core\Route;

Route::pre('/blog')->group(function () {
    Route::get('/', [HomeController::class, 'home']);
});
