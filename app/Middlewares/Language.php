<?php

namespace App\Middlewares;

use zFramework\Core\Facades\Lang;

class Language
{
    public function attempt()
    {
        Lang::locale($_SESSION['lang'] ?? null);
        return true;
    }
}
