<?php
// for https redirect 
# if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") die(header('Location: https://' . ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])));

// Set public dir path
define('PUBLIC_DIR', __DIR__);

// you can move framework location. for example:
# define('BASE_PATH', dirname(__DIR__) . "/zframework");
// Set base path
define('BASE_PATH', dirname(__DIR__));

// Initalize
include(BASE_PATH . '/zFramework/initalize.php');

// Run framework
zFramework\Run::begin();
