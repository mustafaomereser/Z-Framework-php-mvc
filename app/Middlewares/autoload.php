<?php

namespace App\Middlewares;

use zFramework\Core\Middleware;

$list = [
    Language::class
];

Middleware::middleware($list);
