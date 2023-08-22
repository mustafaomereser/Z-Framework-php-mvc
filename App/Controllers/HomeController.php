<?php

namespace App\Controllers;

use App\Models\User;
use App\Requests\Welcome\CommandRequest;
use zFramework\Core\Abstracts\Controller;

class HomeController extends Controller
{

    public function __construct($method)
    {
        echo "<pre>";

        // $user = (new User)->where('users.id', 1)->where('users.id', '=', 1, 'OR')->where([['users.id', 1], ['users.id', '=', 2, 'OR']])->first();
        // print_r($user);
        // print_r($user['posts']()->get());

        print_r((new User)->whereIn('id', [5, 6, 9])->buildSQL());

        exit;
    }

    /** Index page | GET: /
     * @return mixed
     */
    public function index()
    {
        return view('welcome');
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
    public function store(CommandRequest $command)
    {
        $command = $command->validated()['command'];
        $message = shell_exec("php " . base_path('terminal') . " $command --web");
        echo $message;
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
