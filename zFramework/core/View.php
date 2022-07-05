<?php

namespace zFramework\Core;

class View
{
    /**
     * Views Path
     */
    static $path = "../resource/views/";
    private static $binds = [];

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
        if (isset(self::$binds[$view])) $view_parameters = array_merge(self::$binds[$view], $view_parameters);
        // $view_name = $view;
        // $cache_view = (Cache::cache_find_view($view_name) ?? null);
        // if ($cache_view) return $cache_view;

        $view = (self::$path . str_replace('.', '/', $view)) . ".php";
        ob_start();
        extract($view_parameters);
        include($view);
        $view = ob_get_clean();

        $view = self::compile($view, $view_parameters, $template);

        // Cache::cache_view($view_name, $view);

        return $view;
    }

    /** 
     * Bind for ekstra parameters
     * @param string $view
     * @param object $callback
     * @return array;
     */
    public static function bind(string $view, $callback)
    {
        return self::$binds[$view] = $callback();
    }
}
