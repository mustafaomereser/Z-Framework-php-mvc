<?php

namespace Core\Facedas;

use App\Models\User;
use Core\Crypter;

class Auth
{
    static $user = null;

    public static function login($user)
    {
        if (isset($user['id'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }

        return false;
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
        return true;
    }

    public static function check()
    {
        if (isset(self::user()['id'])) return true;
        return false;
    }

    public static function user()
    {
        if (!isset($_SESSION['user_id'])) return false;

        if (self::$user == null) {
            $user = new User;
            self::$user = $user->where('id', '=', $_SESSION['user_id'])->first(); // ->where('api_token', '=', 'test', 'OR')
        }
        if (!@self::$user['id']) return self::logout();

        return self::$user;
    }

    public static function attempt($fields = [], $getUser = false)
    {
        if (self::check()) return false;

        $user = new User;
        $user = $user->select('id');
        foreach ($fields as $key => $val) {
            if ($key == 'password') $val = Crypter::encode($val);
            $user->where($key, '=', $val);
        }
        $user = $user->first();

        if (@$user['id']) {
            $_SESSION['user_id'] = $user['id'];
            return $getUser ? $user : true;
        }

        return false;
    }

    public static function id()
    {
        return self::user()['id'];
    }
}
