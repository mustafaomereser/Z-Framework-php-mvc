<?php


// Do not touch

use zFramework\Core\Facades\Auth;
use zFramework\Core\Route;

Route::pre('/api')->group(function () {
    Route::any('/', function () {
        echo "user: ";
        print_r(Auth::user()['username']);
    });
});
