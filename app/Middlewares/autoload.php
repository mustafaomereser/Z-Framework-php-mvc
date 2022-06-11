<?php

use Core\Middleware;

$list = [
    App\Middlewares\Language::class
];

Middleware::middleware($list);
