<?php
class Users
{
    static $charset = "utf8mb4_general_ci";
    static $table = "users";
    static $db = 'local';

    public static function columns()
    {
        return [
            'id' => ['primary'],
            'username' => ['varchar:50'],
            'password' => ['varchar:50'],
            'email' => ['varchar:50', 'unique'],
            'api_token' => ['varchar:60', 'required'],
            'timestamps',
            'softDelete',
        ];
    }
}
