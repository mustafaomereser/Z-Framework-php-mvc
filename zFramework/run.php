<?php

namespace zFramework;

class Run
{
    static $loadtime;
    static $included = [];
    static $modules  = [];

    public static function includer($_path, $include_in_folder = true, $reverse_include = false, $ext = '.php')
    {
        // $_path = "/$_path";
        if (is_file($_path)) {
            self::$included[] = $_path;
            return include($_path);
        }

        $path = array_values(array_diff(scandir($_path), ['.', '..']));

        if ($reverse_include) $path = array_reverse($path);

        foreach ($path as $inc) {
            $inc = "$_path\\$inc";
            if ((is_dir($inc) && $include_in_folder)) self::includer($inc);
            elseif (file_exists($inc) && strstr($inc, $ext)) {
                include($inc);
                self::$included[] = $inc;
            };
        }
    }

    public static function initProviders()
    {
        foreach (glob(BASE_PATH . "\App\Providers\*.php") as $provider) (new ($provider = str_replace([BASE_PATH . '\\', '.php'], '', $provider)));
        return new self();
    }

    public static function findModules()
    {
        if (!is_dir(base_path('/modules'))) return false;
        $modules         = scan_dir(base_path('/modules'));
        $include_modules = [];
        foreach ($modules as $module) {
            $info = include(base_path("/modules/$module/info.php"));
            if ($info['status']) $include_modules[$info['sort']] = array_merge(['module' => $module], $info);
        }
        ksort($include_modules);
        self::$modules = $include_modules;
        return new self();
    }

    public static function loadModules()
    {
        foreach (self::$modules as $module) self::includer(base_path("/modules/" . $module['module'] . "/route"));
        return new self();
    }

    public static function begin()
    {
        ob_start();
        $start = microtime();
        try {
            # set view options
            \zFramework\Core\View::settingUP([
                'caches' => FRAMEWORK_PATH . '\storage\views',
                'dir'    => BASE_PATH . '\resource\views',
                'suffix' => ''
            ]);

            // includes
            self::includer('..\zFramework\modules\error_handlers');
            self::includer('..\zFramework\modules', false);
            self::includer('..\App\Middlewares\autoload.php');
            self::initProviders()::findModules()::loadModules();
            self::includer('..\route');
            @self::$loadtime = ((microtime() + 0.003) - $start);

            \zFramework\Core\Route::run();
            \zFramework\Core\Facades\Alerts::unset(); // forget alerts
            \zFramework\Core\Facades\JustOneTime::unset(); // forget data
        } catch (\Throwable $errorHandle) {
            errorHandler($errorHandle);
        } catch (\Exception $errorHandle) {
            errorHandler($errorHandle);
        }
    }
}
