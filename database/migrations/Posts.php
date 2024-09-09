<?php

namespace Database\Migrations;

class Posts
{
    static $charset = "utf8mb4_general_ci";
    static $table   = "posts";
    static $db      = "local";

    public static function columns()
    {
        return [
            'id'      => ['primary'],
            'title'   => ['varchar'],
            'user_id' => ['int'],
            'content' => ['text'],

            'timestamps',
            'softDelete'
        ];
    }
}
