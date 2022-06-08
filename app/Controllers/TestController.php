<?php

namespace App\Controllers;

use Core\View;

class TestController
{

    /** Index page | GET: /
     * @return mixed
     */
    public function index()
    {
        return View::view('home.index', ['test' => 'OK'], 'main');
    }

    /** Show page | GET: /id
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        return "show: $id";
    }

    /** Create page | GET: /create
     * @return mixed
     */
    public function create()
    {
        return "create";
    }

    /** Edit page | GET: /id/edit
     * @param integer $id
     * @return mixed
     */
    public function edit($id)
    {
        return "edit: $id";
    }

    /** POST page | POST: /
     * @return mixed
     */
    public function store()
    {
        return 'store';
    }

    /** Update page | PATCH/PUT: /id
     * @return mixed
     */
    public function update($id)
    {
        return "update: $id";
    }

    /** Delete page | DELETE: /id
     * @return mixed
     */
    public function delete($id)
    {
        return "delete: $id";
    }
}
