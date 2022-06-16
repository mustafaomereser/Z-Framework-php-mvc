<?php

function base_path($url = null)
{
    return dirname(__DIR__) . $url;
}

function public_path($url = null)
{
    return base_path('\\' . Core\Facedas\Config::get('app.public')) . $url;
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

function csrf()
{
    return Core\Csrf::csrf();
}

function view()
{
    return call_user_func_array([Core\View::class, 'view'], func_get_args());
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

function request($name = null, $val = NULL)
{
    if ($val === NULL) return $name ? ($_REQUEST[$name] ?? false) : $_REQUEST;
    return $_REQUEST[$name] = $val;
}

function human_filesize($bytes, $decimals = 2)
{
    $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
