<?php

namespace Core\Facedas;

class Response
{
    const list = [
        'json' => 'application/json'
    ];

    private static function do($type, $data = [])
    {
        header("Content-Type: " . self::list[$type]);

        switch ($type) {
            case 'json':
                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                break;
        }

        return $data;
    }

    public static function json($data)
    {
        return self::do(__FUNCTION__, $data);
    }
}
