<?php

namespace Core;

class View
{
    /**
     * Views Path
     */
    static $path = "../resource/views/";

    /**
     * View Compiler (Not true for now)
     * @param string $code
     * @param array $view_parameters
     * @param string $template
     * @return string
     */
    private static function compile(string $code, array $view_parameters = [], string $template = null): string
    {
        if ($template) {
            $template = self::view($template, $view_parameters);
            $code = str_replace('<!--body-->', $code, $template);
        }
        return $code;
    }

    /**
     * Return view
     * @param string $view
     * @param array $view_parameters
     * @param string $template
     * @return string
     */
    public static function view(string $view, array $view_parameters = [], string $template = null): string
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
