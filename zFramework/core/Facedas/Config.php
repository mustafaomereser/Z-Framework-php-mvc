<?php

namespace zFramework\Core\Facedas;

class Config
{
    /**
     * Configs path
     */
    static $path = "config";

    /**
     * @param string $config
     * @param bool $justConfig
     * @return array
     */
    private static function parseUrl(string $config, bool $justConfig = false): array
    {
        $ex = explode(".", $config);

        $path = base_path(self::$path);
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

    /**
     * Get Config
     * @param string $config
     * @return string|array|object
     */
    public static function get(string $config)
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

    /**
     * Update Config set veriables.
     * @return void
     */
    public static function set(string $config, array $sets): void
    {
        $path = self::parseUrl($config, true)[0];
        $arr = self::get($config);

        foreach ($sets as $key => $set)
            if (!empty($set)) $arr[$key] = $set;
            else unset($arr[$key]);

        file_put_contents(strstr($path, '.php') ? $path : "$path.php", "<?php \nreturn " . var_export($arr, true) . ";");
    }
}
