<?php

namespace zFramework\Kernel\Modules;

use Workerman\Worker;
use zFramework\Kernel\Terminal;

class Ws
{
    public static function begin()
    {
        $config = config('app.ws');

        $tcp_worker = new Worker($config['protocol'] . '://' . $config['server'] . ':' . $config['port']);
        $tcp_worker->count = $config['worker-count'];

        $tcp_worker->onConnect = function ($connection) {
            Terminal::text("($connection->id) New Connection", 'green');
        };

        $tcp_worker->onMessage = function ($client, $data) {
            $parseData = json_decode($data, true);
            parse_str($parseData[1], $parseData[1]);

            $ws = (object) [/*'client' => $client,*/'data' => $parseData[0], 'args' => $parseData[1], 'response' => 'NULL'];
            include('ws/api.php');

            if (gettype($ws->response) != 'string') $ws->response = json_encode($ws->response, JSON_UNESCAPED_UNICODE);
            $client->send($ws->response);
        };

        $tcp_worker->onClose = function ($connection) {
            Terminal::text("($connection->id) Connection Close", 'red');
        };

        echo "\e[33mWebSocket running on \e[32m`" . getHostName() . "`\e[33m host: \e[31m\n";
        Worker::runAll();
    }
}
