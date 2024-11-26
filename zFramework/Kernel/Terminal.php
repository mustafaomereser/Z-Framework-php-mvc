<?php

namespace zFramework\Kernel;

class Terminal
{
    static $terminate = false;
    static $commands;
    static $parameters = [];
    static $history;
    static $textlist = [];

    public static function begin($args)
    {
        unset($args[0]);

        if (count($args)) {
            self::$terminate = true;
            return self::parseCommands(implode(' ', $args));
        }

        self::text('[color=red]Terminal fired.[/color]');
        self::clear();

        echo "Usable Modules:" . PHP_EOL . PHP_EOL;
        foreach (array_diff(scandir(FRAMEWORK_PATH . "\Kernel\Modules"), ['.', '..']) as $module) if (!strstr($module, '---')) echo "• " . strtolower(str_replace('.php', '', $module)) . PHP_EOL;

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

        self::$commands   = array_values($commands);
        self::$parameters = $parameters;

        return self::do();
    }

    public static function do()
    {
        self::clear();

        try {
            $module = "\zFramework\Kernel\Modules\\" . ucfirst(strtolower(self::$commands[0]));
            $module::begin();
        } catch (\Throwable $e) {
            self::text("[color=yellow]" . $e->getMessage() . "[/color]");
        }

        if (count(self::$textlist)) echo json_encode(self::$textlist, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (self::$terminate) return null;

        self::$textlist = [];
        return self::readline();
    }

    public static function clear()
    {
        echo str_repeat(PHP_EOL, 50);
        return new self();
    }

    /**
     * CLI send text.
     * @param string $text
     * @param string $color
     * @return void
     */
    public static function text(string $text): void
    {
        $json = in_array('--json', self::$parameters);
        $cli  = !in_array('--web', self::$parameters) && !$json;


        $colors = $cli ? [
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
        ] : [
            'default'       => "#adbac7",
            'white'         => "#cdd9e5",
            'black'         => "#848a93",
            'red'           => "#f47067",
            'green'         => "#57ab5a",
            'yellow'        => "#c69026",
            'blue'          => "#539bf5",
            'magenta'       => "#b083f0",
            'cyan'          => "#39c5cf",
            'light-gray'    => "#8dbac7",
            'dark-gray'     => "#818a95",
            'light-red'     => "#ff938a",
            'light-green'   => "#6bc46d",
            'light-yellow'  => "#daaa3f",
            'light-blue'    => "#6cb6ff",
            'light-magenta' => "#dcbdfb",
            'light-cyan'    => "#56d4dd",
        ];

        while (true) {
            preg_match_all('#\[color=(.+?)\](.+?)\[/color\]#si', $text, $matches);
            if (!$matches[0]) break;

            foreach ($matches[0] as $key => $match) {
                $color   = $matches[1][$key];
                $content = $matches[2][$key];

                $text = str_replace($match, $cli ? ("\e[" . $colors[$color] . "m$content" . "\e[" . $colors['default'] . "m") : ("<font color='" . $colors[$color] . "'>$content</font>"), $text);
            }
        }

        if ($json) self::$textlist[] = $text;
        else echo $text . PHP_EOL;
    }
}
