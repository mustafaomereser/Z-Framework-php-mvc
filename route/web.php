<?php

use App\Models\User;
use zFramework\Core\Route;

// Route::get('/', 'HomeController@index')

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    $user = new User;
    $user->insert([
        'username' => 'test',
        'password' => 'test',
        'email' => 'admin@localhost.com',
        'api_token' => 'test'
    ]);
});

Route::get('/language/{lang}', function ($lang) {
    zFramework\Core\Facades\Lang::locale($lang);
    back();
});

// use App\Controllers\HomeController;

// Route::pre('/admin')->group(function () {
//     Route::pre('/test')->group(function () {
//         Route::resource('/deneme', HomeController::class);
//     });

//     Route::resource('/deneme', HomeController::class);
// });

// echo "<pre style='color: white;'>";
// print_r(Route::$routes);
// echo "</pre>";
