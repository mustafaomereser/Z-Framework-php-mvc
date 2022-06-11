<?php

namespace Core;

class Middleware
{
    public static function middleware($middlewares, $callback = null)
    {
        $declined = [];

        foreach ($middlewares as $middleware) {
            $call = new $middleware();
            if (!call_user_func_array([$call, '__construct'], [])) {
                $declined[] = $middleware;
                if (!$callback) call_user_func_array([$call, 'error'], []);
            }
        }

        return $callback ? $callback($declined) : (count($declined) ? false : true);
    }
}
