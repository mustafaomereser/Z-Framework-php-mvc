<?php

use zFramework\Core\Facades\Auth;
use zFramework\Core\Route;

Route::pre('/api')->csrfNoCheck(true)->group(function () {
    Route::any('/', function () {
        echo "user: ";
        print_r(Auth::user()['username']);
    });
});
