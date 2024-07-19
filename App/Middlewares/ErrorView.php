<?php

namespace App\Middlewares;

use zFramework\Core\Facades\Auth;
use zFramework\Core\Helpers\Http;
use zFramework\Core\Route;

#[\AllowDynamicProperties]
class ErrorView
{
    public function attempt()
    {
        if (Route::has('/admin') && Auth::check()) {
            Http::$error_view = "errors.admin";
        }

        return true;
    }
}
