<?php

namespace App\Middlewares;

use zFramework\Core\Facades\Auth;

class Guest
{
    public function attempt()
    {
        if (!\zFramework\Core\Facades\Auth::check()) return true;
        return false;
    }

    public function error()
    {
        abort();
    }
}
