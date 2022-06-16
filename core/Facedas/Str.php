<?php

namespace Core\Facedas;

class Str
{
    public static function limit($text, $length = 50, $continue = "...")
    {
        if (strlen($text) > $length) $text = substr($text, 0, $length) . $continue;
        return $text;
    }

    public static function rand($length = 5, $unique = false)
    {
        $q = "QWERTYUIOPASDFHJKLZXCVBNMqwertyuopasdfghjklizxcvbnm0987654321";
        $q_count = strlen($q) - 1;
        $r = "";
        for ($x = $length; $x > 0; $x--) $r .= $q[rand(0, $q_count)];
        return $r . ($unique ? uniqid('', true) : null);
    }

    public static function slug($text, string $divider = '-')
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', $divider, $text)));;
    }
}
