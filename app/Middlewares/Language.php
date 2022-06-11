<?php

namespace App\Middlewares;

use Core\Facedas\Lang;

class Language
{
    public function __construct()
    {
        Lang::locale($_SESSION['lang'] ?? null);
        return true;
    }
}
