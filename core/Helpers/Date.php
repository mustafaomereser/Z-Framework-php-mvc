<?php

namespace Core\Helpers;

class Date
{
    public static function setLocale($set)
    {
        return date_default_timezone_set($set);
    }

    public static function locale()
    {
        return date_default_timezone_get();
    }

    public static function format($date, $format = 'd.m.Y')
    {
        return date($format, (is_string($date) ? strtotime($date) : $date));
    }

    public static function now($format = 'd.m.Y H:i')
    {
        return date($format);
    }

    public static function timestamp()
    {
        return date('Y-m-d H:i:s');
    }
}
