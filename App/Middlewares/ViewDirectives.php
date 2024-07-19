<?php

namespace App\Middlewares;

use zFramework\Core\Facades\Auth;
use zFramework\Core\Helpers\Http;
use zFramework\Core\Route;
use zFramework\Core\View;

#[\AllowDynamicProperties]
class ViewDirectives
{
    public function attempt()
    {
        // for error pages example.
        if (Route::has('/admin') && Auth::check()) {
            Http::$error_view = "errors.admin";
        }


        # Custom Directives
        View::directive('page', function ($page) {
            return '<?php if (isset($_GET["page"]) && $_GET["page"] === \'' . $page . '\'): ?>';
        });

        View::directive('endpage', function () {
            return '<?php endif; ?>';
        });

        return true;
    }
}
