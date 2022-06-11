<?php

namespace Core;

class Route
{
    static $routes = [];
    static $calledRoute = null;
    static $called = false;
    static $preURL = null;


    private static function parser($data, $method, $options)
    {
        if (self::$preURL && $data[0] == '/') $data[0] = null;

        $_uri = strtok(strtok(uri(), '#'), '?');
        $_url = str_replace("//", "/", (self::$preURL . $data[0]));

        //
        $inf = ['url' => $_url, 'method' => $method];
        if (@$options['name'])
            self::$routes[$options['name']] = $inf;
        else
            self::$routes[] = $inf;
        //

        //
        $uri = explode('/', $_uri);
        $url = explode('/', $_url);
        unset($uri[0], $url[0]);
        $url = array_values($url);
        $uri = array_values($uri);
        //

        $parameters = [];
        foreach ($uri as $key => $val) {
            $urlVal = @$url[$key];
            if ((!$urlVal || !$val) || !preg_match('/[^a-zA-Z0-9]+/i', $urlVal)) continue;

            $url[$key] = $val;
            $parameters[str_replace(['{', '}'], '', $urlVal)] = $val;
        }


        return compact('parameters', 'uri', 'url');
    }

    private static function call(array $data, $method = null)
    {
        $callback = $data[1] ?? null;
        $options = $data[2] ?? [];
        extract(self::parser($data, $method, $options));

        //
        Middleware::middleware($options['middlewares'] ?? []);
        //

        if (($url != $uri || ($method && $method != method())) || self::$called == true) return;
        if ((method() != 'get' && @$_REQUEST['_token'] != Csrf::get()) && @$options['no-csrf'] != true) abort(400, 'CSRF token geçersiz.');


        self::$called = true;
        self::$calledRoute = $data[0];

        switch (gettype($callback)) {
            case 'object':
                break;

            case 'array':
                $callback = [new $callback[0](), $callback[1]];
                break;

            default:
                abort();
        }

        echo call_user_func_array($callback, $parameters);
    }

    public static function name($name, $data = [])
    {
        if(!isset(self::$routes[$name])) return;

        $url = self::$routes[$name]['url'];
        foreach ($data as $key => $val) $url = str_replace("{" . $key . "}", $val, $url);
        return $url;
    }

    public static function redirect($url, $to)
    {
        self::call([$url, function () use ($to) {
            return redirect($to);
        }]);
    }

    public static function any()
    {
        self::call(func_get_args());
    }

    public static function get()
    {
        self::call(func_get_args(), __FUNCTION__);
    }

    public static function post()
    {
        self::call(func_get_args(), __FUNCTION__);
    }

    public static function patch()
    {
        self::call(func_get_args(), __FUNCTION__);
    }

    public static function put()
    {
        self::call(func_get_args(), __FUNCTION__);
    }

    public static function delete()
    {
        self::call(func_get_args(), __FUNCTION__);
    }

    public static function resource($url, $callback, $options = [])
    {
        self::get($url, [$callback, 'index'], $options);
        self::post($url, [$callback, 'store'], $options);

        self::get("$url/create", [$callback, 'create'], $options);
        self::get("$url/{id}", [$callback, 'show'], $options);
        self::get("$url/{id}/edit", [$callback, 'edit'], $options);

        self::patch("$url/{id}", [$callback, 'update'], $options);
        self::put("$url/{id}", [$callback, 'update'], $options);

        self::delete("$url/{id}", [$callback, 'delete'], $options);
    }
}
