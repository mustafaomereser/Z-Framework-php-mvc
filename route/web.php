<?php

use App\Controllers\TestController;
use Core\Route;

Route::redirect('/test', '/');
Route::resource('/', TestController::class);