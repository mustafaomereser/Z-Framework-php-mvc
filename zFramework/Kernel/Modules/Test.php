<?php

namespace zFramework\Kernel\Modules;

use zFramework\Kernel\Terminal;

class Test
{
    public static function begin()
    {
        foreach ([
            'default',
            'white',
            'black',
            'red',
            'green',
            'yellow',
            'blue',
            'magenta',
            'cyan',
            'light-gray',
            'dark-gray',
            'light-red',
            'light-green',
            'light-yellow',
            'light-blue',
            'light-magenta',
            'light-cyan'
        ] as $c) Terminal::text("[color=$c]$c" . "[/color]");
    }
}
