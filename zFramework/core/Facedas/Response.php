<?php

namespace zFramework\Core\Facedas;

class Response
{
    /**
     * Response type list
     */
    const list = [
        'json' => 'application/json'
    ];

    /**
     * Result Method
     * @param string $type
     * @param array $data
     * @return string|mixed
     */
    private static function do(string $type, array $data = [])
    {
        header("Content-Type: " . self::list[$type]);

        switch ($type) {
            case 'json':
                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                break;
        }

        return $data;
    }

    /**
     * Type Json
     * @param array $data
     * @return self
     */
    public static function json(array $data): self
    {
        return self::do(__FUNCTION__, $data);
    }
}
