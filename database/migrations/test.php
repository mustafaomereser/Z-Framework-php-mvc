<?php
class Test
{
    static $charset = "utf8mb4_general_ci";
    static $table   = "test";
    static $db      = "local";
    static $prefix  = "test";

    public static function columns()
    {
        return [
            'id' => ['primary'],
            'timestamps',
            'softDelete'
        ];
    }
}
