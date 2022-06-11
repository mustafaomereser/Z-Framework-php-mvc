<?php

namespace Core\Facedas;

class Lang
{
    static $locale = null;
    static $path = null;

    private static function canSelect($lang)
    {
        $path = base_path() . "\\resource\lang\\$lang";
        if (!is_dir($path)) return false;
        return $path;
    }


    public static function locale($lang = null, $syncSession = true)
    {
        $lang = ($lang ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        if (!$path = self::canSelect($lang)) return self::locale(Config::get('app.lang'));
        if ($syncSession) $_SESSION['lang'] = $lang;

        self::$locale = $lang;
        self::$path = $path;

        return true;
    }

    public static function currentLocale()
    {
        return self::$locale;
    }

    public static function get($_name)
    {
        $name = explode('.', $_name);
        $lang = self::$path . "\\" . $name[0] . ".php";

        if (!is_file($lang)) return $_name;

        $lang = include($lang);
        unset($name[0]);

        foreach ($name as $val) if (isset($lang[$val])) $lang = $lang[$val];

        return $lang;
    }
}
