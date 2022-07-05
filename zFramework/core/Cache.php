<?php

namespace zFramework\Core;

class Cache
{
    /**
     * Cache a data and get it for before timeout.
     * 
     * @param string $name
     * @param object $callback / Must be Closure Object and it must do return.
     * @param int $timeout
     * @return mixed
     */
    public static function cache(string $name, $callback, int $timeout = 5)
    {
        if (!isset($_SESSION['caching'][$name]) || time() > $_SESSION['caching_timeout'][$name]) {
            $_SESSION['caching'][$name] = $callback();
            $_SESSION['caching_timeout'][$name] = (time() + $timeout);
        }

        return $_SESSION['caching'][$name];
    }
}
