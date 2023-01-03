<?php

namespace App\Middlewares;

use zFramework\Core\Facades\Auth;

class Guest
{
    public function attempt()
    {
        if (!Auth::check()) return true;
        return false;
    }

    public function error()
    {
        abort();
    }
}
