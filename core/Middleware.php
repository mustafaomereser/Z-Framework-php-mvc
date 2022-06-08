<?php

namespace Core;

class Middleware
{
    public static function middleware($middlewares, $callback = null)
    {
        $declined = 0;

        foreach ($middlewares as $middleware) {
            $middleware = new $middleware();
            if (!call_user_func_array([$middleware, '__construct'], [])) {
                $declined++;
                call_user_func_array([$middleware, 'error'], []);
            }
        }
        return $callback ? $callback($declined) : ($declined ? false : true);
    }
}
