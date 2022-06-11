<?php

namespace App\Controllers;

use Core\Facedas\Alerts;
use Core\View;

class TestController
{

    public function __construct()
    {
    }

    /** Index page | GET: /
     * @return mixed
     */
    public function index()
    {
        Alerts::success('sa')::danger('sa');
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
        echo "store";
    }

    /** Update page | PATCH/PUT: /id
     * @return mixed
     */
    public function update($id)
    {
        return "Update: $id";
    }

    /** Delete page | DELETE: /id
     * @return mixed
     */
    public function delete($id)
    {
        echo "Delete $id";
    }
}
