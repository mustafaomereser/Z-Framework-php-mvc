<?php

namespace App\Middlewares;

use zFramework\Core\Facades\Auth as FacadesAuth;

class Auth
{
    public function attempt()
    {
        if (FacadesAuth::check()) return true;
        return false;
    }

    public function error()
    {
        abort(401);
    }
}
