<?php

use zFramework\Core\Route;

Route::pre('/blog')->group(function () {
    Route::get('/', function () {
        return "blog module is created.";
    });
});
