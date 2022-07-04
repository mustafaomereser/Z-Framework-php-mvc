<?php

namespace zFramework;

class Run
{
    static $loadtime;

    static $included = [];

    public static function includer($_path, $include_in_folder = true, $reverse_include = false, $ext = '.php')
    {
        // $_path = "/$_path";
        if (is_file($_path)) return include($_path);

        $path = array_values(array_diff(scandir($_path), ['.', '..']));

        if ($reverse_include) $path = array_reverse($path);

        foreach ($path as $inc) {
            $inc = "$_path/$inc";
            if ((is_dir($inc) && $include_in_folder)) self::includer($inc);
            elseif (file_exists($inc) && strstr($inc, $ext)) include($inc);
        }
    }

    public static function begin()
    {
        $start = microtime();
        try {
            // includes
            self::includer('../zFramework/modules/error_handlers');
            self::includer('../zFramework/modules', false);

            // Automatic include from zFramework/initalize.php
            // self::includer('../zFramework/core');
            // self::includer('../app');

            self::includer('../app/Middlewares/autoload.php');
            self::includer('../route');
            self::includer('../zFramework/modules/error_http');
            self::$loadtime = ((microtime() + 0.003) - $start);

            \zFramework\Core\Route::run();
            // forget alerts
            \zFramework\Core\Facedas\Alerts::unset();
        } catch (\Throwable $errorHandle) {
            errorHandler(array_values((array) $errorHandle));
        }
    }
}
