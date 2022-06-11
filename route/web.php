<?php

use Core\Crypter;
use Core\Facedas\Auth;
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

if (Auth::attempt(['username' => 'test', 'password' => 'test'])) {
    echo 'kullanıcı girişi yapıldı.';
}
