<?php

namespace App\Middlewares;

use Core\Facedas\Auth as FacedasAuth;

class Auth
{
    public function __construct()
    {
        if (FacedasAuth::check()) return true;
        return false;
    }

    public function error()
    {
        abort(401);
    }
}
