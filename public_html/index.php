<?php
// Set base path
define('BASE_PATH', dirname(__DIR__));

// Initalize
include(BASE_PATH . '/zFramework/Initalize.php');

// Run framework
zFramework\Run::begin();