<?php

use Core\Route;

use App\Controllers\testController;

// Route::get('/', function () {
//     echo View::view('home.index', ['welcome' => "Hoşgeldin"], 'main');

// Route::get('/{id}', function ($id) {
//     echo "test id: $id";
// }, [
//     'name' => 'test'
// ]);


// echo Route::name('test', ['id' => '1']);
// });

// Route::get('/', [testController::class, 'index']);

// Route::post('/', function () {
//     echo "post page";
// });

Route::resource('/', testController::class, [
    'no-csrf' => true
]);
