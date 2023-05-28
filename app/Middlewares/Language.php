<?php

namespace App\Middlewares;

use zFramework\Core\Facades\Lang;

class Language
{
    public function attempt()
    {
        Lang::locale($_COOKIE['lang'] ?? null);
        return true;
    }
}
