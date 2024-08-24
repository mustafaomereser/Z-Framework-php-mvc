<?php

namespace App\Controllers;

use App\Requests\Welcome\CommandRequest;
use zFramework\Core\Abstracts\Controller;
use zFramework\Core\Facades\Alerts;
use zFramework\Core\Validator;
use zFramework\Kernel\Terminal;

class HomeController extends Controller
{

    public function __construct($method)
    {

        // echo "<pre>";

        // $validate = Validator::validate([
        //     'test'     => 'admin',
        //     'password' => 1231231
        // ], [
        //     'test'     => ['unique:users key=username'],
        //     'password' => ['type:string', 'min:30']
        // ], [], function ($errors) {
        //     print_r($errors);
        // });
        // // print_r(Alerts::get());
        // print_r($validate);
        // exit;

        // print_r((new User)->sqlDebug(true)->paginate());
        // exit;
        // $data    = [
        //     ['test', 'naber'],
        //     ['naber', 'test']
        // ];
        // $compare = [
        //     ['test', 'naber'],
        //     ['yiğit', 'mustafa']
        // ];

        // $output = _Array::filter($data, $compare, function ($data, $compare) {
        //     return $data[0] != $compare[0] || $data[1] != $compare[1];
        // });

        // print_r($output);
        // exit;
    }

    /** Index page | GET: /
     * @return mixed
     */
    public function index()
    {
        return view('app.pages.welcome');
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
        $message = \zFramework\Kernel\Terminal::begin(["terminal", $command, "--web"]);

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
