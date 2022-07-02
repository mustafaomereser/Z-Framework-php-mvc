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

function back($add = null)
{
    return redirect(($_SERVER['HTTP_REFERER'] ?? '/') . $add);
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

function config()
{
    return call_user_func_array([Core\Facedas\Config::class, 'get'], func_get_args());
}

function _l()
{
    return call_user_func_array([Core\Facedas\Lang::class, 'get'], func_get_args());
}

function human_filesize($bytes, $decimals = 2)
{
    $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";

    if (preg_match('/linux/i', $u_agent)) $platform = 'linux';
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) $platform = 'mac';
    elseif (preg_match('/windows|win32/i', $u_agent)) $platform = 'windows';

    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/OPR/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/OPR/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    } elseif (preg_match('/Edg/i', $u_agent)) {
        $bname = 'Edge';
        $ub = "Edge";
    } elseif (preg_match('/Trident/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edg/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edg/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    preg_match_all($pattern, $u_agent, $matches);

    $i = count($matches['browser']);
    if ($i != 1) {
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) $version = $matches['version'][0];
        else $version = @$matches['version'][1];
    } else {
        $version = $matches['version'][0];
    }

    return [
        'userAgent' => $u_agent,
        'name'      => $bname,
        'ub'        => $ub,
        'version'   => $version ?? '?',
        'platform'  => $platform,
        'pattern'   => $pattern
    ];
}
