<?php

namespace App\Middlewares;

use zFramework\Core\Middleware;

$list = [
    Language::class,
    ErrorView::class
];

Middleware::middleware($list);
