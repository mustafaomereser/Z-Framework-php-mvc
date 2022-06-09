<?php

use App\Models\User;

$start_time = microtime();

session_start();

$connected_databases = [];
$databases = [
    'local' => ['mysql:host=localhost;dbname=test;charset=utf8mb4', 'root', '123123']
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


$finish_time = microtime() + 0.003;

if (@$_REQUEST['load_time']) echo "<script>console.log(`%c Page is in " . number_Format(($finish_time - $start_time), 3, ',', '.') . "ms loaded.`, 'background: #000; color: #bada55')</script>";

$user = new User;
echo "<pre>";
print_r(
    $user->join('RIGHT', 'join_test', ['join_test.user_id', '=', 'users.id'])->select('join_test.id as joinID, users.id as userID')->buildSQL()
);
