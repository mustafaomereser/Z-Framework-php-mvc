<?php

namespace App\Providers;

use App\Models\User;
use zFramework\Core\View;

class ViewProvider
{
    public function __construct()
    {
        View::bind('test', function () {
            $user = new User;
            return [
                'users' => $user->get()
            ];
        });
    }
}
