<?php

use Core\Facedas\Auth;
use Core\Route;

// Do not touch
Route::$preURL = "/api";
if (@$_REQUEST['user_token']) Auth::api_login($_REQUEST['user_token']);
//


Route::get('/test', function () {
    echo "API sayfası / user_id: " . Auth::id();
});


// Do not touch
if (@$_REQUEST['user_token']) Auth::logout();
//