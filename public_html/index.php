<?php
session_start();

$connected_databases = [];
$databases = [
    'local' => ['mysql:host=localhost;dbname=test;charset=utf8mb4', 'root', null]
];


function includer($_path, $include_in_folder = true, $reverse_include = false, $ext = '.php')
{
    if (is_file($_path)) return include($_path);

    $path = scandir($_path);
    unset($path[array_search('.', $path)], $path[array_search('..', $path)]);
    $path = array_values($path);

    if ($reverse_include) $path = array_reverse($path);

    foreach ($path as $inc) {
        $inc = "$_path/$inc";
        if ((is_dir($inc) && $include_in_folder))
            includer($inc);
        elseif (file_exists($inc) && strstr($inc, $ext))
            include($inc);
    }
}

includer('../core');
includer('../app');
includer('../modules', false);
includer('../route', true, true);
includer('../modules/error_handlers');