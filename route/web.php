<?php

use App\Models\User;
use zFramework\Core\Crypter;
use zFramework\Core\Facades\Str;
use zFramework\Core\Route;

// use App\Controllers\HomeController;
// Route::get('/', [HomeController::class, 'index']);

Route::get('/', 'HomeController@index');

Route::get('/test', function () {
    $user = new User;

    // $user->insert([
    //     'username'  => Str::rand(20),
    //     'password'  => Crypter::encode('test'),
    //     'email'     => Str::rand(5) . "@" . Str::rand(4) . ".com",
    //     'api_token' => Str::rand(60)
    // ]);

    $user->delete();

    return view('test');
})->name('test');

Route::get('/language/{lang}', function ($lang) {
    zFramework\Core\Facades\Lang::locale($lang);
    back();
});
