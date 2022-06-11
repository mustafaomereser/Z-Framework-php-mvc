<?php

use Core\Facedas\Config;

function base_path($url = null)
{
    return dirname(__DIR__) . $url;
}

function public_path($url = null)
{
    return base_path('\\' . Config::get('app.public')) . $url;
}

function host()
{
    $protocol = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://";
    $port = $_SERVER['SERVER_PORT'];

    $dont_show_port = [80, 443];
    return $protocol . $_SERVER['SERVER_NAME'] . (!empty($port) && !in_array($port, $dont_show_port) ? ":$port" : null);
}

function redirect($url = "/")
{
    die(header("Location: $url"));
}

function back()
{
    if (@$_SERVER['HTTP_REFERER'])
        return redirect($_SERVER['HTTP_REFERER']);
}

function uri()
{
    return @$_SERVER['REQUEST_URI'];
}

function method()
{
    return strtolower($_POST['_method'] ?? $_SERVER['REQUEST_METHOD']);
}

function inputMethod($method = "GET")
{
    echo '<input type="hidden" name="_method" value="' . strtoupper($method) . '" />';
}

function ip()
{
    return ($_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']));
}

function abort($code = 418, $message = null)
{
    if ($message) echo $message;
    die(http_response_code($code));
}

function request($name = null)
{
    return $name ? @$_REQUEST[$name] : $_REQUEST;
}

function response($type, $data = [])
{
    switch ($type) {
        case "json":
            header("Content-Type: application/json");
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            break;
    }

    return $data;
}
