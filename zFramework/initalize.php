<?php
// Initalize settings

date_default_timezone_set('ETC/UTC');
session_start();

include(BASE_PATH . '/database/connections.php'); #db connections strings
include(BASE_PATH . "/zFramework/run.php");
include(BASE_PATH . '/zFramework/vendor/autoload.php');

spl_autoload_register(function ($class) {
    include BASE_PATH . "/$class.php";
});