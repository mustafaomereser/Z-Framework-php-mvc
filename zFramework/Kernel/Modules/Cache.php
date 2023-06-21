<?php

namespace zFramework\Kernel\Modules;

use zFramework\Kernel\Terminal;

class Cache
{
    public static function begin()
    {
        self::{Terminal::$commands[1]}();
    }

    public static function clear()
    {
        global $storage_path;

        $option = Terminal::$commands[2];

        $list = array_values(array_diff(scandir($storage_path), ['.', '..']));
        if (!in_array($option, $list)) return Terminal::text("Wrong Option!\nOptions: " . implode(', ', $list) . ".", 'red');

        Terminal::text("Processing...", 'yellow');

        $dir = glob($storage_path . "/$option/*");
        foreach ($dir as $unlink) unlink($unlink);

        Terminal::clear();
        Terminal::text("$option (" . count($list) . " qty) caches cleared!", 'green');
    }
}
