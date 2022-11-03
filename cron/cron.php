<?php
$startTime = time();
use zFramework\Run;

//
define('BASE_PATH', str_replace('\\', '/', dirname(__DIR__)));
define('FRAMEWORK_PATH', BASE_PATH . "/zFramework");
include(BASE_PATH . '/zFramework/initalize.php');
Run::includer('../zFramework/modules', false);
//