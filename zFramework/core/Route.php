<?php

namespace zFramework\Core;

use zFramework\Core\Facades\Auth;
use zFramework\Core\Facades\Lang;

class Route
{
    static $routes = [];
    static $called = false;
    static $calledRoute = null;
    static $calledInformations = [];

    // Changable Parameters
    static $prefix_URL = null; // Route's before write url. example: /admin
    static $csrfNoCheck = false; // if that be true csrf not match return always true 
    //

    public static function findRoute($name, $data = [])
    {
        if (!isset(self::$routes[$name])) return $name;
        $url = self::$routes[$name]['url'];
        foreach ($data as $key => $val) $url = str_replace("{" . $key . "}", $val, $url);
        return (host() . script_name()) . $url;
    }

    public static function redirect($url, $to)
    {
        self::call([$url, function () use ($to) {
            return redirect($to);
        }]);
        return new self();
    }

    public static function any()
    {
        self::call(func_get_args());
        return new self();
    }

    public static function get()
    {
        self::call(func_get_args(), __FUNCTION__);
        return new self();
    }

    public static function post()
    {
        self::call(func_get_args(), __FUNCTION__);
        return new self();
    }

    public static function patch()
    {
        self::call(func_get_args(), __FUNCTION__);
        return new self();
    }

    public static function put()
    {
        self::call(func_get_args(), __FUNCTION__);
        return new self();
    }

    public static function delete()
    {
        self::call(func_get_args(), __FUNCTION__);
        return new self();
    }

    public static function ws($method = 'GET', $url, $callback)
    {
        self::pre('/ws')->group(function () use ($method, $url, $callback) {
            self::call([$url, $callback], strtolower($method));
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

    // Private Methods
    private static function parser($data, $method, $options)
    {
        if (self::$prefix_URL && $data[0] == '/') $data[0] = null;

        $_uri = strtok(strtok(uri(), '#'), '?');
        $_url = str_replace("//", "/", (self::$prefix_URL . $data[0]));

        //
        $inf = ['url' => $_url, 'method' => $method];
        if (@$options['name']) self::$routes[$options['name']] = $inf;
        else self::$routes[] = $inf;
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
            if ((!$val || !$urlVal) || (!strstr($urlVal, '{') || !strstr($urlVal, '}'))) continue;

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


        // Middlewares
        Middleware::middleware($options['middlewares'] ?? []);
        //

        // Verify
        if (self::$called == true || ($url != $uri || ($method && $method != method()))) return;
        if (!Csrf::check($options['no-csrf'] ?? self::$csrfNoCheck)) abort(406, Lang::get('errors.csrf.no-verify'));
        //
        self::$called = true;
        self::$calledRoute = $data[0];

        if (!in_array(gettype($callback), ['object', 'array', 'string'])) throw new \Exception('This type not valid.');

        Route::api_user(0, $_REQUEST['user_token'] ?? ''); // login if url is api
        switch (gettype($callback)) {
            case 'string':
                $callback = explode('@', $callback);
                $findController = strtok(findFile($callback[0], 'php', 'App\Controllers'), '.');
                $callback = [new $findController($callback[1]), $callback[1]];
                break;
            case 'array':
                $callback = [new $callback[0]($callback[1]), $callback[1]];
                break;
        }

        self::$calledInformations = [$callback, $parameters];
    }

    public static function run()
    {
        if (count(self::$calledInformations) != 2) throw new \Exception('Route can not run.');
        echo call_user_func_array(self::$calledInformations[0], self::$calledInformations[1]);
        self::api_user(1); // logout if url is api;
    }

    public function name($name)
    {
        $name = self::nameOrganize(self::$prefix_URL . "/$name");

        $key = @end(array_keys(self::$routes));
        $route = self::$routes[$key];
        unset(self::$routes[$key]);

        self::$routes[$name] = $route;

        return new self();
    }

    private static function nameOrganize($val)
    {
        $name = str_replace("..", ".", rtrim(ltrim(str_replace('/', '.', $val), '.'), '.'));
        if (strstr($name, '..')) return self::nameOrganize($name);
        return $name;
    }

    // Groups: Start
    static $groups = [];
    static $groupsReverse = [];
    public static function pre(string $url): self
    {
        self::$groups['prefix_URL'] = self::$prefix_URL . $url;
        return new self();
    }

    public static function csrfNoCheck(bool $arg): self
    {
        self::$groups['csrfNoCheck'] = $arg;
        return new self();
    }

    public static function group($callback)
    {
        foreach (self::$groups as $key => $setting) {
            self::$groupsReverse[$key] = self::${$key};
            self::${$key} = $setting;
        }
        $callback = $callback();
        foreach (self::$groupsReverse as $key => $reverse) self::${$key} = $reverse;
        return $callback;
    }
    // Groups: end


    static $api_logged_in = false;
    public static function api_user(int $type = 0, $token = '')
    {
        if (!self::$api_logged_in) {
            if (@explode('/', self::$prefix_URL)[1] != 'api') return;
            if ($type == 0) {
                self::$api_logged_in = true;
                Auth::token_login($token);
            }
        }

        if ($type == 1) {
            self::$api_logged_in = false;
            Auth::logout();
        }
    }
}
