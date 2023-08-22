<?php

namespace zFramework\Core;

use ReflectionFunction;
use ReflectionMethod;
use zFramework\Core\Facades\Lang;

class Route
{
    static $routes      = [];
    static $calledRoute = null;

    public static function findRoute($name, $data = [], $return_bool = false)
    {
        $route_is_exists = true;
        $return = $name;
        if (!isset(self::$routes[$name])) $route_is_exists = false;

        if ($route_is_exists) {
            $url = self::$routes[$name]['url'];
            foreach ($data as $key => $val) $url = str_replace(["{" . $key . "}", "{?" . $key . "}"], $val, $url);

            while (strstr($url, '//')) $url = str_replace(['//'], ['/'], $url);

            $return = (host() . script_name()) . rtrim($url, '/');
        }

        if ($return_bool) return $route_is_exists;
        return $return;
    }

    private static function nameOrganize($val)
    {
        $name = str_replace("..", ".", rtrim(ltrim(str_replace('/', '.', $val), '.'), '.'));
        if (strstr($name, '..')) return self::nameOrganize($name);
        return $name;
    }

    public static function name($name)
    {
        $name = self::nameOrganize(@self::$groups['pre'] . "/$name");
        $old_key = @end(array_keys(self::$routes));
        self::$routes[$name] = array_pop(self::$routes);
        if (!is_null(self::$calledRoute) && @self::$calledRoute['name'] == $old_key) self::$calledRoute['name'] = $name;
        return new self();
    }

    public static function redirect($url, $to)
    {
        self::any($url, function () use ($to) {
            http_response_code(302);
            die(header("Location: $to"));
        });
        return new self();
    }

    public static function any()
    {
        self::call(null, func_get_args());
        return new self();
    }

    public static function get()
    {
        self::call(__FUNCTION__, func_get_args());
        return new self();
    }

    public static function post()
    {
        self::call(__FUNCTION__, func_get_args());
        return new self();
    }

    public static function patch()
    {
        self::call(__FUNCTION__, func_get_args());
        return new self();
    }

    public static function put()
    {
        self::call(__FUNCTION__, func_get_args());
        return new self();
    }

    public static function delete()
    {
        self::call(__FUNCTION__, func_get_args());
        return new self();
    }

    public static function ws($method = 'GET', $url, $callback)
    {
        self::pre('/ws')->group(function () use ($method, $url, $callback) {
            self::call($method, [$url, $callback]);
        });

        return new self();
    }

    public static function resource($url, $callback, $options = [])
    {
        self::get($url, [$callback, 'index'], $options)->name("$url.index");
        self::post($url, [$callback, 'store'], $options)->name("$url.store");
        self::get("$url/create", [$callback, 'create'], $options)->name("$url.create");
        self::get("$url/{id}", [$callback, 'show'], $options)->name("$url.show");
        self::get("$url/{id}/edit", [$callback, 'edit'], $options)->name("$url.edit");
        self::patch("$url/{id}", [$callback, 'update'], $options)->name("$url.update");
        self::put("$url/{id}", [$callback, 'update'], $options)->name("$url.update");
        self::delete("$url/{id}", [$callback, 'delete'], $options)->name("$url.delete");

        return new self();
    }

    private static function dispatch($method, $args)
    {
        while (strstr($args[0], '//')) $args[0] = str_replace(['//'], ['/'], $args[0]);


        $method = mb_strtoupper($method);
        $URI    = explode('/', substr(strtok($_SERVER['REQUEST_URI'], '?'), 1));
        $URL    = explode('/', substr($args[0], 1));

        self::$routes[] = [
            'url'    => $args[0],
            'method' => $method
        ];

        $match      = 0;
        $parameters = [];
        foreach ($URL as $key => $row) {
            @$column = $URI[$key];

            if (strstr($row, '{') && strstr($row, '}')) {
                if (!strlen($column)) {
                    if (strstr($row, '{?')) $match++;
                    continue;
                }

                $URL[$key] = $column;
                $match++;
                $parameters[str_replace(['{?', '{', '}'], '', $row)] = $column;
            } else {
                if ($column == $row) $match++;
            }

            if (empty($URL[$key]) && count($URL) != 1) unset($URL[$key]);
        }

        $URI = array_values($URI);
        $URL = array_values($URL);

        $match = ((empty($method) || $method == method()) && $URI == $URL ? 1 : 0); #($match > 0 && (count($URL) - count($URI) == 0))) ? 1 : 0;

        // echo "<pre>";
        // echo "\n";
        // print_r("Method: $method = " . method() . "\n");
        // print_r("Match: $match\n");
        // echo "URI:";
        // print_r($URI);
        // echo "URL:";
        // print_r($URL);
        // echo "original URL:";
        // print_r($args[0]);
        // var_dump($URI == $URL);
        // echo str_repeat('-', 50);
        // echo "</pre>";

        return compact('match', 'parameters', 'URI', 'URL');
    }

    public static function call($method, $args)
    {
        $args[0] = @self::$groups['pre'] . $args[0];

        $dispatch = self::dispatch($method, $args);
        if (self::$calledRoute != null || !$dispatch['match']) return;
        if (!Csrf::check(isset(self::$groups['no-csrf']))) abort(406, Lang::get('errors.csrf.no-verify'));

        if (@self::$groups['middlewares']) {
            $middleware = Middleware::middleware(self::$groups['middlewares'][0], function ($declines) {
                if (!count($declines)) return true;
                if (self::$groups['middlewares'][1]) self::$groups['middlewares'][1]($declines);
                return false;
            });

            if (!$middleware) return;
        }

        self::$calledRoute = [
            'name'       => @end(array_keys(self::$routes)),
            'callback'   => $args[1],
            'parameters' => $dispatch['parameters']
        ];
    }

    public static function run()
    {
        if (self::$calledRoute === null) abort(404);

        $callback = self::$calledRoute['callback'];
        if (!in_array(gettype($callback), ['object', 'array', 'string'])) throw new \Exception('This type not valid.');

        switch (gettype($callback)) {
            case 'string':
                $callback    = explode('@', $callback);
                $callback[0] = strtok(findFile($callback[0], 'php', 'App\Controllers'), '.');
                $callback    = [new $callback[0]($callback[1]), $callback[1]];
                break;
            case 'array':
                $callback = [new $callback[0]($callback[1]), $callback[1]];
                break;
        }

        try {
            $reflection = new ReflectionMethod($callback[0], $callback[1]);
        } catch (\Throwable $e) {
            $reflection = new ReflectionFunction($callback);
        }

        $parameters = $reflection->getParameters();
        foreach ($parameters as $parameter) {
            $name       = $parameter->getName();
            $dependence = (string) $parameter->getType();
            if (!empty(self::$calledRoute['parameters'][$name]) || !class_exists($dependence)) continue;
            self::$calledRoute['parameters'][$name] = new $dependence;
        }

        echo call_user_func_array($callback, self::$calledRoute['parameters']);
    }

    # Groups: Start
    static $groups     = [];
    static $add_groups = [];

    public static function noCSRF()
    {
        self::$add_groups['no-csrf'] = true;
        return new self();
    }

    public static function pre($prefix)
    {
        self::$add_groups['pre'] = @self::$groups['pre'] . $prefix;
        return new self();
    }

    public static function middleware(array $list, $callback = null)
    {
        self::$add_groups['middlewares'] = [$list, $callback];
        return new self();
    }

    public static function group($callback)
    {
        $groupsReverse = [];
        foreach (self::$add_groups as $key => $setting) {
            $groupsReverse[$key] = self::$groups[$key] ?? null;
            self::$groups[$key]  = $setting;
        }
        $callback = $callback();
        foreach ($groupsReverse as $key => $reverse) self::$groups[$key] = $reverse;
        self::$add_groups = [];
        return $callback;
    }
    # Groups: End
}
