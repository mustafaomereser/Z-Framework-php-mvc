<?php

namespace zFramework\Kernel\Helpers;

use zFramework\Kernel\Terminal;

class Ask
{
    public static function do(string $question, object $callback)
    {
        Terminal::text("$question (Y, N)");
        $answer = readline(">");
        if (strtoupper($answer) != 'Y') return Terminal::text('[color=blue]Ask declined.[/color]');
        return $callback();
    }
}
