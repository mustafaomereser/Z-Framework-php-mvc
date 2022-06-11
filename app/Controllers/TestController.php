<?php

namespace App\Controllers;

use Core\Facedas\Alerts;
use Core\Facedas\Config;
use Core\Helpers\File;
use Core\View;

class TestController
{
    /** Index page | GET: /
     * @return mixed
     */
    public function index()
    {
        return View::view('home.index', [], 'main');
    }

    /** Show page | GET: /id
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        echo "Show: $id";
    }

    /** Create page | GET: /create
     * @return mixed
     */
    public function create()
    {
        echo "create";
    }

    /** Edit page | GET: /id/edit
     * @param integer $id
     * @return mixed
     */
    public function edit($id)
    {
        echo "Edit: $id";
    }

    /** POST page | POST: /
     * @return mixed
     */
    public function store()
    {
        echo File::resizeImage(File::upload('/assets/images', $_FILES['file']), 500, 300);
    }

    /** Update page | PATCH/PUT: /id
     * @return mixed
     */
    public function update($id)
    {
        Config::set('test', [
            'value' => (Config::get('test.value') ?? 0) + 1
        ]);

        Alerts::success("Success data($id) is updated.");
        // return response('json', Alerts::get());
        return back();
    }

    /** Delete page | DELETE: /id
     * @return mixed
     */
    public function delete($id)
    {
        echo "Delete $id";
    }
}
