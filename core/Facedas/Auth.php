<?php

namespace Core\Facedas;

use App\Models\User;
use Core\Crypter;

class Auth
{
    /**
     * When you use ::user() method you must fill that and if you again use ::user() get from this parameter.
     */
    static $user = null;

    /**
     * Login from a User model result array.
     * @param array $user
     */
    public static function login(array $user): bool
    {
        if (isset($user['id'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }

        return false;
    }

    /**
     * Login with user's api_token
     * @param string $token
     */
    public static function api_login(string $token): void
    {
        $user = new User;
        $user = $user->select('id')->where('api_token', '=', $token)->first();
        if (@$user['id']) self::login($user);
    }

    /**
     * Logout User
     * @return bool
     */
    public static function logout(): bool
    {
        self::$user = null;
        unset($_SESSION['user_id']);
        return true;
    }

    /**
     * Check User logged in
     * @return bool
     */
    public static function check(): bool
    {
        if (isset(self::user()['id'])) return true;
        return false;
    }

    /**
     * Get current logged user informations
     * @return array|self
     */
    public static function user(): array
    {
        if (!isset($_SESSION['user_id'])) return false;

        if (self::$user == null) {
            $user = new User;
            self::$user = $user->where('id', '=', $_SESSION['user_id'])->first(); // ->where('api_token', '=', 'test', 'OR')
        }
        if (!@self::$user['id']) return self::logout();

        return self::$user;
    }

    /**
     * Attempt for login.
     * @param array $fields
     * @param bool $getUser
     * @return bool
     */
    public static function attempt(array $fields = [], bool $getUser = false): bool
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

    /**
     * Get Current logged in user's id
     * @return integer
     */
    public static function id(): int
    {
        return self::user()['id'];
    }
}
