<?php

namespace Core\Facedas;

use App\Models\User;
use Core\Validator;

class Auth
{
    static $user = null;

    public static function login($user)
    {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }

    public static function api_login($token)
    {
        $user = new User;
        $user = $user->select('id')->where('api_token', '=', $token)->first();
        if (@$user['id']) self::login($user);
    }

    public static function logout()
    {
        self::$user = null;
        unset($_SESSION['user_id']);
    }

    public static function check()
    {
        if (@$_SESSION['user_id']) return true;
        return false;
    }

    public static function user()
    {
        if (!self::check()) return false;

        if (self::$user == null) {
            $user = new User;
            self::$user = $user->where('id', '=', $_SESSION['user_id'])->first(); // ->where('api_token', '=', 'test', 'OR')
        }

        return self::$user;
    }

    public static function id()
    {
        return self::user()['id'];
    }
}
