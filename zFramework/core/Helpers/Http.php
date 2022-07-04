<?php

namespace zFramework\Core\Helpers;

class Http
{
    /**
     * Check is XMLHttpRequest Or Normal Request
     */
    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}
