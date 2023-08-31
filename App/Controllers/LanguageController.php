<?php

namespace App\Controllers;

use zFramework\Core\Abstracts\Controller;

class LanguageController extends Controller
{

    public function __construct()
    {
        //
    }

    public function set($lang)
    {
        \zFramework\Core\Facades\Lang::locale($lang);
        back();
    }
}
