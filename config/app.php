<?php

return [
    'ws' => [
        'protocol'     => 'tcp',
        'server'       => getHostByName(getHostName()),
        'port'         => 5000,
        'worker-count' => 4
    ],

    'debug'    => true,

    'lang'     => 'tr', // if browser haven't language in Languages list automatic choose that default lang.

    'title'    => 'Z Framework Project',
    'public'   => 'public_html',

    'crypt'    => [
        'key'  => 'cryptkey',
        'salt' => 'ThisSaltIsSecret'
    ],

    'version'  => '1.0.0',

    'mysql'    => [
        'mysqldump' => 'D:\wamp\bin\mysql\mysql5.7.19\bin\mysqldump'
    ]
];
