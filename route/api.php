<?php

use zFramework\Core\Facades\Auth;
use zFramework\Core\Helpers\Http;
use zFramework\Core\Route;

Route::pre('/api')->csrfNoCheck(true)->group(function () {
    Route::get('/', function () {
        $text = "Welcome to API Route.\nIf you wanna user login.\n/api?user_token={user_token}\n";
        if (Auth::check()) {
            $text .= "\nUser:";
            $text .= var_export(Auth::user(), true);
        }

        return Http::isAjax() ? $text : str_replace("\n", '<br />', $text);
    });
});
