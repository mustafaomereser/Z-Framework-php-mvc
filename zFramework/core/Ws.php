<?php

namespace zFramework\Core;

use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

class Ws
{
    public static function send($message, $commands = null, $callback, $contentType = null)
    {
        $data = shell_exec("php " . __DIR__ . "\Ws.php $message \"$commands\"");
        if ($contentType == 'json') header('Content-Type: application/json');

        return $callback($data);
    }

    public static function shell($args)
    {
        $message = $args[1];
        $args = $args[2];

        $dirname = dirname(__DIR__);
        require_once "$dirname/vendor/autoload.php";

        $config = include(dirname($dirname) . '/config/app.php');
        $config = $config['ws'];

        $worker = new Worker;
        $worker->onWorkerStart = function () use ($config, $message, $args) {
            $ws_connection = new AsyncTcpConnection($config['protocol'] . '://' . $config['server'] . ':' . $config['port']);

            $ws_connection->onConnect = function ($connection) use ($message, $args) {
                $connection->send(json_encode([$message, $args], JSON_UNESCAPED_UNICODE));
            };

            $ws_connection->onMessage = function ($connection, $data) {
                print_r($data);
                Worker::stopAll();
            };

            $ws_connection->onError = function () {
                echo json_encode(['status' => -1, 'message' =>  'Server Error!']);
                Worker::stopAll();
            };

            $ws_connection->connect();
        };

        Worker::runAll();
    }
}

if (php_sapi_name() == "cli") Ws::shell($argv);
