<?php

namespace zFramework\Core\Facedas;

class Lang
{
    static $locale = null;
    static $path = null;

    /**
     * is selectable?
     * @param string $lang
     * @return string
     */
    private static function canSelect(string $lang): string
    {
        $path = base_path("resource/lang/$lang");
        if (!is_dir($path)) return false;
        return $path;
    }

    /**
     * Lang list
     * @return array
     */
    public static function list(): array
    {
        return array_values(array_diff(scandir(base_path("resource/lang")), ['.', '..']));
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

    /**
     * Current Locale
     * @return string
     */
    public static function currentLocale(): string
    {
        return self::$locale;
    }

    /**
     * Get Lang string or array
     * @param string $_name
     * @param array $data
     * @return array|string
     */
    public static function get(string $_name, array $data = [])
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
