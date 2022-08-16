<?php
// for https redirect 
# if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") die(header('Location: https://' . ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])));

// Set base path
define('BASE_PATH', dirname(__DIR__));
define('FRAMEWORK_PATH', BASE_PATH . "/zFramework");

// Initalize
include(BASE_PATH . '/zFramework/Initalize.php');

// Run framework
zFramework\Run::begin();
