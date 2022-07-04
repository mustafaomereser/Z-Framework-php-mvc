<?php


// Do not touch

use zFramework\Core\Facedas\Auth;
use zFramework\Core\Route;

Route::$preURL = "/api";
if (@$_REQUEST['user_token']) Auth::api_login($_REQUEST['user_token']);
//






// Do not touch
if (@$_REQUEST['user_token']) Auth::logout();
//