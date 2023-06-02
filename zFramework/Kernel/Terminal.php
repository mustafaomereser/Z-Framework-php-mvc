<?php

namespace zFramework\Kernel;

class Terminal
{
    static $terminate = false;
    static $commands;
    static $parameters;
    static $history;

    public static function begin()
    {
        self::text('Terminal fired.');
        self::clear();

        echo "Usable Modules:" . PHP_EOL . PHP_EOL;
        foreach (array_diff(scandir(FRAMEWORK_PATH . "\Kernel\Modules"), ['.', '..']) as $module) echo "• " . strtolower(str_replace('.php', '', $module)) . PHP_EOL;

        return self::readline();
    }

    public static function readline()
    {
        echo PHP_EOL;
        return self::parseCommands(readline('> '));
    }

    public static function parseCommands($commands)
    {
        // command add to history.
        // unset(self::$history[array_search($commands, self::$commands)]);
        self::$history[] = $commands;
        readline_add_history($commands);
        //

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
            self::text($e->getMessage(), 'yellow');
        }

        return (!self::$terminate ? self::readline() : null);
    }

    public static function clear()
    {
        echo str_repeat(PHP_EOL, 50);
    }

    /**
     * CLI send text.
     * @param string $text
     * @param string $color
     * @return void
     */
    public static function text(string $text, string $color = 'default'): void
    {
        $colors = [
            'default'       => 39,
            'white'         => 97,
            'black'         => 30,
            'red'           => 31,
            'green'         => 32,
            'yellow'        => 33,
            'blue'          => 34,
            'magenta'       => 35,
            'cyan'          => 36,
            'light-gray'    => 37,
            'dark-gray'     => 90,
            'light-red'     => 91,
            'light-green'   => 92,
            'light-yellow'  => 93,
            'light-blue'    => 94,
            'light-magenta' => 95,
            'light-cyan'    => 96,
        ];

        echo "\e[" . $colors[$color] . "m$text\n\e[" . $colors['default'] . "m";
    }
}
