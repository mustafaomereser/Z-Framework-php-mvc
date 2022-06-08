<?php

namespace App\Middlewares;

class Guest
{
    public function __construct()
    {
        if (!@$_SESSION['user_id']) return true;
    }

    public function error()
    {
        abort();
    }
}
