<?php
include('../vendor/autoload.php');
date_default_timezone_set('Etc/UTC');

use Core\Route;

$start_time = microtime();
session_start();

$connected_databases = [];

function includer($_path, $include_in_folder = true, $reverse_include = false, $ext = '.php')
{
    if (is_file($_path)) return include($_path);

    $path = array_values(array_diff(scandir($_path), ['.', '..']));

    if ($reverse_include) $path = array_reverse($path);

    foreach ($path as $inc) {
        $inc = "$_path/$inc";
        if ((is_dir($inc) && $include_in_folder))
            includer($inc);
        elseif (file_exists($inc) && strstr($inc, $ext))
            include($inc);
    }
}
include('../database/connections.php'); #db connections strings

// includes
includer('../modules', false);
includer('../core');
includer('../app');
includer('../route', true, true);
includer('../modules/error_handlers');
echo Route::run();

// forget alerts
Core\Facedas\Alerts::unset();
//

@$finish_time = microtime() + 0.003;
if (@$_REQUEST['load_time']) echo "<script>console.log(`%c Page is in " . number_format(($finish_time - $start_time), 3, ',', '.') . "ms loaded.`, 'background: #000; color: #bada55')</script>";
