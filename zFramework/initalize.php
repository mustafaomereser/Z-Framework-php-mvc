<?php
// Initalize settings
date_default_timezone_set('ETC/UTC');

// Session: Start
session_save_path(FRAMEWORK_PATH . "/storage/sessions");
ini_set('session.gc_probability', 1);
session_start();
// Session: End

$connected_databases = [];
$databases = include(BASE_PATH . '/database/connections.php'); #db connections strings

include(BASE_PATH . '/zFramework/run.php');
include(BASE_PATH . '/zFramework/vendor/autoload.php');

spl_autoload_register(function ($class) {
    zFramework\Run::includer(BASE_PATH . "/$class.php");
    if (method_exists($class, 'init')) $class::init();    
});
