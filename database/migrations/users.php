<?php
class Users
{

    static $table = "users";
    static $db = 'local';

    public static function up()
    {
        return [
            'id' => ['primary']
        ];
    }
}
