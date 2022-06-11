<?php

use App\Controllers\TestController;
use Core\Route;
use Core\View;

Route::redirect('/test', '/');
// Route::resource('/', TestController::class);


Route::get('/fuck/ah/oh/man/yes/{id}/omg/{suckmydick}', function ($id, $suckmydick) {
    echo "$id - $suckmydick <br>";
    return View::view('home.index');
}, [
    'name' => 'test'
]);


echo Route::name('test', ['id' => 'hi', 'suckmydick' => 'hello']);
