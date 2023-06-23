<?php

namespace zFramework\Kernel\Modules;

use zFramework\Kernel\Terminal;

class Make
{
    static $assets_path;
    static $assets;

    public static function begin()
    {
        self::assets();
        self::do();
    }

    private static function assets()
    {
        self::$assets_path = FRAMEWORK_PATH . "\zhelper\make\\";
        $assets = glob(self::$assets_path . "*");
        foreach ($assets as $key => $val) {
            unset($assets[$key]);
            $assets[mb_strtolower(str_replace(self::$assets_path, '', $val))] = $val;
        }
        self::$assets = $assets;
    }

    private static function parseName()
    {
        $namespace = explode('\\', str_replace('/', '\\', Terminal::$commands[2]));
        $name      = ucfirst(end($namespace));
        unset($namespace[array_search(end($namespace), $namespace)]);

        $namespace = implode('\\', $namespace);

        return compact('namespace', 'name');
    }

    private static function do()
    {
        $method = Terminal::$commands[1];
        @$make = self::$assets[$method];
        if (!$make) return Terminal::text('This is not makeable. List:' . implode(', ', array_keys(self::$assets)));

        if (in_array('--resource', Terminal::$parameters)) $make .= "_resource";

        extract(self::parseName());
        $make = str_replace(['{namespace}', '{name}'], [(strlen($namespace) ? "\\$namespace" : null), $name], file_get_contents($make));

        if (!$make) return Terminal::text('This is not acceptable.');

        return self::{$method}($make);
    }

    private static function controller($make)
    {
        return self::save('App\Controllers', $make);
    }

    private static function middleware($make)
    {
        return self::save('App\Middlewares', $make);
    }

    private static function migration($make)
    {
        global $databases;
        return self::save(
            'database\migrations',
            str_replace(
                ['{table}', '{dbname}'],
                [(Terminal::$parameters['table'] ?? self::parseName()['name']), (Terminal::$parameters['dbname'] ?? array_keys($databases)[0])],
                $make
            )
        );
    }

    private static function seeder($make)
    {
        return self::save('database\seeders', $make);
    }


    private static function model($make)
    {
        return self::save(
            'App\Models',
            str_replace(
                ['{table}'],
                [(Terminal::$parameters['table'] ?? self::parseName()['name'])],
                $make
            )
        );
    }

    private static function observer($make)
    {
        return self::save('App\Observers', $make);
    }

    private static function save($to, $content)
    {
        extract(self::parseName());

        $to = base_path("$to\\$namespace");
        @mkdir($to, 0777, true);
        $save_to = "$to\\$name.php";

        if (file_exists($save_to)) return Terminal::text("[color=red]This is already exists. $save_to" . "[/color]");
        file_put_contents($save_to, $content);
        Terminal::text("[color=green]Asset is created to $save_to" . "[/color]");
    }
}
