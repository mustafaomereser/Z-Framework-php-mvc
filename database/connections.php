<?php
return [
    'local' => [
        'mysql:host=localhost;dbname=z_framework;charset=utf8mb4', 'root', '', 'options' => [
            [\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION]
        ]
    ],
    'local2' => [
        'mysql:host=localhost;dbname=trip_project;charset=utf8mb4', 'root', '', 'options' => [
            [\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION]
        ]
    ]
];
