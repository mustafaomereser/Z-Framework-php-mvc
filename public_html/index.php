<?php
// for https redirect 
# if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") die(header('Location: https://' . ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])));

// Set base path
define('BASE_PATH', dirname(__DIR__));

// Initalize
include(BASE_PATH . '/zFramework/initalize.php');

// Run framework
zFramework\Run::begin();
