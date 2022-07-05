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
            elseif (file_exists($inc) && strstr($inc, $ext)) {
                include($inc);
                self::$included[] = $inc;
            };
        }
    }

    public static function initProviders()
    {
        foreach (glob(BASE_PATH . "\App\Providers\*.php") as $provider) {
            $provider = str_replace([BASE_PATH . '\\', '.php'], '', $provider);
            new $provider();
        }
    }

    public static function begin()
    {
        ob_start();
        $start = microtime();
        try {
            // includes
            self::includer('../zFramework/modules/error_handlers');
            self::includer('../zFramework/modules', false);

            // Automatic include from zFramework/initalize.php
            // self::includer('../zFramework/core');
            // self::includer('../app');

            self::includer('../app/Middlewares/autoload.php');
            self::initProviders();
            self::includer('../route');
            self::includer('../zFramework/modules/error_http');
            self::$loadtime = ((microtime() + 0.003) - $start);


            \zFramework\Core\Route::run();
            \zFramework\Core\Facades\Alerts::unset(); // forget alerts
        } catch (\Throwable $errorHandle) {
            errorHandler($errorHandle);
        } catch (\Exception $errorHandle) {
            errorHandler($errorHandle);
        }
    }
}
