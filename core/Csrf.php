<?php

namespace Core;

use Core\Facedas\Str;

class Csrf
{
    static $timeOut = (10 * 60);

    public static function csrf()
    {
        echo "<input type='hidden' name='_token' value='" . self::get() . "' />";
    }

    public static function get()
    {
        if (!@$_SESSION['csrf_token'] || time() > @$_SESSION['csrf_token_timeout']) self::set();
        return $_SESSION['csrf_token'];
    }

    public static function set()
    {
        $_SESSION['csrf_token_timeout'] = time() + self::$timeOut;
        $_SESSION['csrf_token'] = Str::rand(30);
    }

    public static function unset()
    {
        unset($_SESSION['csrf_token']);
    }

    public static function remainTimeOut()
    {
        return @$_SESSION['csrf_token_timeout'] - time();
    }

    public static function check($alwaysTrue = false)
    {
        if ((method() != 'get' && request('_token') != self::get()) && $alwaysTrue != true) return false;
        return true;
    }
}
