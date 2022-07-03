<?php

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    if($errno == 2) return;
    echo "<b>Custom error:</b> [$errno] $errstr<br>";
    echo " Error on line $errline in $errfile<br>";
}

// Set user-defined error handler function
set_error_handler("myErrorHandler");
