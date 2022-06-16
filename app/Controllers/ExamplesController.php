<?php

namespace App\Controllers;

use App\Models\User;
use Core\Crypter;
use Core\Facedas\Str;
use Core\Validator;
use Core\View;

class ExamplesController
{

    public function __construct()
    {
        $this->user = new User;
    }

    /** Index page | GET: /
     * @return mixed
     */
    public function index($createdUser = [])
    {
        $user = new User;

        return view('examples', [
            'users' => $user->paginate(1),
            'users2' => $user->paginate(2, 'page_2'),
            'createdUser' => $createdUser
        ]);
    }

    /** Show page | GET: /id
     * @param integer $id
     * @return mixed
     */
    public function show($id)
    {
        abort(404);
    }

    /** Create page | GET: /create
     * @return mixed
     */
    public function create()
    {
        abort(404);
    }

    /** Edit page | GET: /id/edit
     * @param integer $id
     * @return mixed
     */
    public function edit($id)
    {
        abort(404);
    }

    /** POST page | POST: /
     * @return mixed
     */
    public function store()
    {
        $validate = Validator::validate($_POST, [
            'username' => ['required'],
            'password' => ['required', 'same:re-password'],
            're-password' => ['required'],
            'email' => ['required', 'email', 'unique:users cl=email,db=local']
        ]);
        unset($validate['re-password']);

        $validate['password'] = Crypter::encode(request('password'));
        $validate['api_token'] = Str::rand(30, true);

        $createdUser = $this->user->insert($validate);

        return $this->index($createdUser);
    }

    /** Update page | PATCH/PUT: /id
     * @return mixed
     */
    public function update($id)
    {
        abort(404);
    }

    /** Delete page | DELETE: /id
     * @return mixed
     */
    public function delete($id)
    {
        abort(404);
    }
}
