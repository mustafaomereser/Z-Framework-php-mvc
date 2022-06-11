<?php

namespace Core\Facedas;

class Config
{
    static $path = "../config";

    private static function parseUrl($config, $justConfig = false)
    {
        $ex = explode(".", $config);

        $path = self::$path . "";
        $arg = "";

        $config_found = 0;
        foreach ($ex as $g) {
            if (!$config_found) {
                $path .= "/$g";
                if (is_file("$path.php")) {
                    $config_found = 1;
                    $path .= ".php";
                }
            } elseif (!$justConfig) {
                $arg .= ".$g";
            }
        }

        $return = [$path];
        if ($arg) $return[] = ltrim($arg, ".");

        return $return;
    }

    public static function get($config)
    {
        $arr = self::parseUrl($config);
        if (!is_file($arr[0])) return;

        $config = include($arr[0]);

        if (isset($arr[1])) {
            $keys = explode('.', $arr[1]);
            foreach ($keys as $key) if (isset($config[$key])) $config = $config[$key];
        }

        return $config;
    }

    public static function set($config, $sets)
    {
        $path = self::parseUrl($config, true)[0];
        $arr = self::get($config);

        foreach ($sets as $key => $set)
            if (!empty($set))
                $arr[$key] = $set;
            else
                unset($arr[$key]);

        file_put_contents(strstr($path, '.php') ? $path : "$path.php", "<?php \nreturn " . var_export($arr, true) . ";");
    }
}
