<?php
// If not found a Route abort 404 not found page.
if (\zFramework\Core\Route::$calledRoute == null) abort(404);
