<?php
function base_path($url = null)
{
    return dirname(__DIR__) . $url;
}

function public_path($url = null)
{
    return base_path('\public_path') . $url;
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
    return redirect($_SERVER['HTTP_REFERER']);
}

function uri()
{
    return $_SERVER['REQUEST_URI'];
}

function method()
{
    return strtolower($_REQUEST['_method'] ?? $_SERVER['REQUEST_METHOD']);
}

function ip()
{
    return ($_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']));
}

function abort($code = 418)
{
    die(http_response_code($code));
}
