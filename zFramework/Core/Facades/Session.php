<?php

namespace zFramework\Core\Facades;

class Session
{
    /**
     * Set a session.
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public static function set(string $key, mixed $value): self
    {
        $_SESSION[$key] = $value;
        return new self();
    }

    /**
     * Get session from key.
     * @param $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? NULL;
    }

    /**
     * Forget a session by key.
     * @param string $key
     * @return self
     */
    public static function delete(string $key): self
    {
        unset($_SESSION[$key]);
        return new self();
    }
}
