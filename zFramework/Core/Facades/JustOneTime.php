<?php

namespace zFramework\Core\Facades;

class JustOneTime
{

    static private $session_name = 'just-one-time';

    /**
     * Set just one time data.
     * @param string $name
     * @param mixed $val
     * @return self
     */
    public static function set(string $name, mixed $val): self
    {
        $_SESSION[self::$session_name][$name] = $val;
        return new self();
    }

    /**
     * Get data
     * @param string $name
     * @return mixed
     */
    public static function get(string $name): mixed
    {
        return @$_SESSION[self::$session_name][$name];
    }

    /**
     * Unset All Data.
     * @return void
     */
    public static function unset()
    {
        unset($_SESSION[self::$session_name]);
    }
}
