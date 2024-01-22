<?php

use App\Controllers\AuthController;
use zFramework\Core\Route;
use App\Controllers\HomeController;
use App\Controllers\LanguageController;
use App\Models\User;
use zFramework\Core\Facades\Alerts;
use zFramework\Core\Helpers\File;

Route::get('/test', function () {
    print_r((new User)->select(['id'])->get());

    print_r(Alerts::get());
});

Route::get('/language/{lang}', [LanguageController::class, 'set'])->name('language');

Route::middleware([App\Middlewares\Guest::class])->group(function () {
    Route::get('/auth', [AuthController::class, 'auth'])->name('auth-form');
    Route::post('/sign-in', [AuthController::class, 'signin'])->name('sign-in');
    Route::post('/sign-up', [AuthController::class, 'signup'])->name('sign-up');
});

Route::middleware([App\Middlewares\Auth::class])->group(function () {
    Route::any('/sign-out', [AuthController::class, 'signout'])->name('sign-out');
});

Route::get('/auth-content', [AuthController::class, 'content'])->name('auth-content');

Route::resource('/', HomeController::class);
