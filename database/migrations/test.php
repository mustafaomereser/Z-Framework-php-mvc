<?php
class Test
{

    static $table = "tests";
    static $db = 'local';

    public static function up()
    {
        return [
            'id' => ['primary'],
            'username' => ['varchar:52', 'charset:utf8:general_ci'],
            'password' => ['varchar:50', 'charset:utf8:general_ci'],
            'email' => ['varchar:50', 'charset:utf8:general_ci', 'unique'],
        ];
    }
}
