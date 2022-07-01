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

    public static function list()
    {
        return array_values(array_diff(scandir(base_path() . "\\resource\lang"), ['.', '..']));
    }

    public static function locale($lang = null, $syncSession = true)
    {
        $lang = ($lang ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        if (!$path = self::canSelect($lang)) return self::locale(Config::get('app.lang') ?? self::list()[0]);
        if ($syncSession) $_SESSION['lang'] = $lang;

        self::$locale = $lang;
        self::$path = $path;

        return true;
    }

    public static function currentLocale()
    {
        return self::$locale;
    }

    public static function get($_name, array $data = [])
    {
        $name = explode('.', $_name);
        $lang = self::$path . "\\" . $name[0] . ".php";
        if (!is_file($lang)) return null;

        $lang = include($lang);
        unset($name[0]);

        foreach ($name as $val) $lang = $lang[$val] ?? null;

        foreach ($data as $key => $val) $lang = str_replace("{" . $key . "}", $val, $lang);

        return $lang;
    }
}
