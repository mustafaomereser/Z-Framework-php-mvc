<?php
return [
    'local' => ['mysql:host=localhost;dbname=z_framework;charset=utf8mb4', 'root', null, 'options' => [
        [\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION]
    ]],
];
