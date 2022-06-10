<?php

namespace Core\Facedas;

class Lang
{
    static $locale = "";

    public static function locale($lang = null)
    {
        $lang = ($lang ?? (Config::get('app.lang') ?? null));

        $lang = base_path() . "\\resource\lang\\$lang";
        if (!is_dir($lang)) return false;

        $_SESSION['lang'] = $lang;
        self::$locale = $lang;

        return true;
    }

    public static function get($_name)
    {
        $name = explode('.', $_name);
        $lang_file = include(self::$locale . "\\" . $name[0] . ".php");
        unset($name[0]);

        foreach ($name as $val) if (isset($lang_file[$val])) $lang_file = $lang_file[$val];

        return $lang_file;
    }
}
