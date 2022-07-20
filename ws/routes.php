<?php
// Proje içi kullanım sağlıklı değildir, fakat yine de kullanım desteklenmektedir.

use zFramework\Core\Route;
use zFramework\Core\Ws;


Route::ws('get', '/test', function () {
    return Ws::send('test', null, function ($data) {
        echo $data;
    });
});

Route::ws('get', '/test/{id}', function ($id) {
    return Ws::send('user', "id=$id", function ($data) {
        return view('test', ['user' => json_decode($data, true)]);
    });
})->name('ws.test');