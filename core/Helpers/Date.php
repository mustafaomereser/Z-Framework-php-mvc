<?php

namespace Core\Helpers;

class Date
{
    /**
     * Set Timezone
     * @param string $set
     * @return int
     */
    public static function setLocale(string $set): int
    {
        return date_default_timezone_set($set);
    }

    /**
     * Get Current Timezone
     * @return string
     */
    public static function locale(): string
    {
        return date_default_timezone_get();
    }

    /**
     * Date reformat
     * @param string|int $date
     * @param string $format
     * @return string 
     */
    public static function format(string $date, string $format = 'd.m.Y'): string
    {
        return date($format, (is_string($date) ? strtotime($date) : $date));
    }

    /**
     * Get Now With Date
     * @param string $format
     * @return string
     */
    public static function now(string $format = 'd.m.Y H:i'): string
    {
        return date($format);
    }

    /**
     * Current Timestamp For Mysql Or Mssql
     * @return string
     */
    public static function timestamp(): string
    {
        return date('Y-m-d H:i:s');
    }
}
