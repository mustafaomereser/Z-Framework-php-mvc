<?php

use Core\Facedas\Alerts;
use Core\Facedas\Lang;
use Core\Helpers\File;
use Core\Route;
use Core\View;

Route::any('/', function () {

    $upload = File::upload('/uploads', $_FILES['file']);
    if($upload) Alerts::success('Dosya yüklendi.');

    $resize = File::resizeImage($upload, 100, 100);

    if($resize) Alerts::success('Resim boyutlandırıldı.');

    return View::view('welcome');
});

Route::get('/language/{lang}', function ($lang) {
    Lang::locale($lang);
    back();
});
