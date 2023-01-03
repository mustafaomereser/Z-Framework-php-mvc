<?php

namespace zFramework\Core\Facades;

class Alerts
{

    /**
     * Set just one time alerts.
     * @param mixed
     * @return self
     */
    private static function set(): self
    {
        $_SESSION['alerts'][] = func_get_args();
        return new self();
    }

    /**
     * Get Alerts
     * @return array
     */
    public static function get(): array
    {
        return $_SESSION['alerts'] ?? [];
    }

    /**
     * Unset All Alerts.
     * @return void
     */
    public static function unset()
    {
        unset($_SESSION['alerts']);
    }

    /**
     * Set a danger Alert
     * @return self
     */
    public static function danger($text): self
    {
        return self::set(__FUNCTION__, $text);
    }

    /**
     * Set a success Alert
     * @return self
     */
    public static function success($text): self
    {
        return self::set(__FUNCTION__, $text);
    }

    /**
     * Set a warning Alert
     * @return self
     */
    public static function warning($text): self
    {
        return self::set(__FUNCTION__, $text);
    }

    /**
     * Set a info Alert
     * @return self
     */
    public static function info($text): self
    {
        return self::set(__FUNCTION__, $text);
    }
}
