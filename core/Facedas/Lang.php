<?php

namespace Core\Facedas;

class Lang
{
    static $locale = null;
    static $path = null;

    public static function locale($lang = null, $syncSession = true)
    {
        $lang = ($lang ?? (Config::get('app.lang') ?? null));

        $path = base_path() . "\\resource\lang\\$lang";
        if (!is_dir($path)) return false;

        if ($syncSession) $_SESSION['lang'] = $lang;

        self::$locale = $lang;
        self::$path = $path;

        return true;
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
