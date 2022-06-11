<?php

namespace Core\Facedas;

class Alerts
{

    private static function set()
    {
        $_SESSION['alerts'][] = func_get_args();
        return new self();
    }

    public static function get()
    {
        return $_SESSION['alerts'] ?? [];
    }

    public static function unset()
    {
        unset($_SESSION['alerts']);
    }

    public static function danger($text)
    {
        return self::set(__FUNCTION__, $text);
    }

    public static function success($text)
    {
        return self::set(__FUNCTION__, $text);
    }

    public static function warning($text)
    {
        return self::set(__FUNCTION__, $text);
    }

    public static function info($text)
    {
        return self::set(__FUNCTION__, $text);
    }
}
