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

    // Abort to http response.
    public static function abort(int $code = 418, $message = null)
    {
        http_response_code($code);
        $view = @view("errors.$code", ['message' => $message], 'errors.main');
        die(self::isAjax() ? json_encode(['code' => $code, 'message' => $message], JSON_UNESCAPED_UNICODE) : (!empty($view) ? $view : $message));
    }
}
