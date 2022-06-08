<?php

namespace App\Middlewares;

class Auth
{
    public function __construct()
    {
        if (@$_SESSION['user_id']) return true;
    }

    public function error()
    {
        abort(401);
    }
}
