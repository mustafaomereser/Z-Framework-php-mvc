<?php

namespace Core;
class View
{
    static $path = "../resource/views/";

    private static function compile($code, $view_parameters = [], $template = null)
    {
        if ($template) {
            $template = self::view($template, $view_parameters);
            $code = str_replace('<!--body-->', $code, $template);
        }

        return $code;
    }

    public static function view($view, $view_parameters = [], $template = null)
    {
        $view = (self::$path . str_replace('.', '/', $view)) . ".php";

        ob_start();
        extract($view_parameters);
        include($view);
        $view = ob_get_clean();

        $view = self::compile($view, $view_parameters, $template);

        return $view;
    }
}
