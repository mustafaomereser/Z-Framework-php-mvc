<?php

namespace zFramework\Kernel;

class Terminal
{
    static $commands;
    static $parameters;

    public static function init()
    {
        self::clear();
        return self::readline();
    }

    private static function clear()
    {
        echo str_repeat(PHP_EOL, 50);
    }

    public static function readline()
    {
        echo PHP_EOL;
        return self::parseCommands(readline('> '));
    }

    public static function parseCommands($commands)
    {
        $commands   = explode(' ', $commands);
        $parameters = [];

        // parse it
        foreach ($commands as $key => $command) {
            if (!strstr($command, '=')) continue;
            unset($commands[$key]);
            $command = explode('=', $command);
            $parameters[$command[0]] = $command[1];
        }

        foreach ($commands as $key => $command) {
            if (!strstr($command, '--')) continue;
            unset($commands[$key]);
            $parameters[] = $command;
        }
        //

        self::$commands   = $commands;
        self::$parameters = $parameters;

        return self::do();
    }

    public static function do()
    {
        // print_r(self::$commands);
        // print_r(self::$parameters);

        self::clear();

        try {
            $module = "\zFramework\Kernel\Modules\\" . ucfirst(mb_strtolower(self::$commands[0]));
            $module::begin();
        } catch (\Throwable $e) {
            self::text($e->getMessage());
        }

        return self::readline();
    }


    public static function text($text)
    {
        // echo PHP_EOL;

        // // $matches = [
        // //     '~\[color=(.*?)\](.*?)\[/color\]~s'
        // // ];

        // // $parse_replace = [];

        // // foreach ($matches as $match) {
        // //     preg_match_all($match, $text, $result);
        // //     if (!count($result[0])) continue;
        // // }


        echo $text;
    }
}
