<?php

use Core\Facedas\Auth;
use Core\Route;

Route::$preURL = "/api";

if (@$_REQUEST['user_token']) Auth::api_login();

Route::get('/test', function () {
    echo "API sayfası";
});
