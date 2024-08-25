<?php

namespace zFramework\Kernel\Modules;

use zFramework\Core\Facades\Config;
use zFramework\Core\Facades\Str;
use zFramework\Kernel\Terminal;

class Security
{
    public static function begin()
    {
        self::{Terminal::$commands[1]}();
    }

    public static function key()
    {
        if (in_array('--regen', Terminal::$parameters)) {
            Config::set('crypt', [
                'key'  => Str::rand(30),
                'salt' => Str::rand(30)
            ]);

            Terminal::text('[color=green]Security crypt key is regenerated.[/color]');
        }
    }
}
