<?php

namespace App\Controllers;

use App\Models\User;
use zFramework\Core\Abstracts\Controller;
use zFramework\Core\Facades\Cookie;
use zFramework\Core\Validator;

class HomeController extends Controller
{

    public function __construct($method)
    {
        $this->user = new User;
    }

    /** Index page | GET: /
     * @return mixed
     */
    public function index()
    {

        // var_dump(Cookie::set('selam', 'hey'));
        // var_dump(Cookie::get('selam'));
        // // var_dump(Cookie::delete('selam'));

        // exit;

        // or you can set model like `(new User)->get();`
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

    private function convertBash($string)
    {
        $colors = [
            '/\[0;30m(.*?)\[0m/s' => '<span class="black">$1</span>',
            '/\[0;31m(.*?)\[0m/s' => '<span class="red">$1</span>',
            '/\[0;32m(.*?)\[0m/s' => '<span class="green">$1</span>',
            '/\[0;33m(.*?)\[0m/s' => '<span class="brown">$1</span>',
            '/\[0;34m(.*?)\[0m/s' => '<span class="blue">$1</span>',
            '/\[0;35m(.*?)\[0m/s' => '<span class="purple">$1</span>',
            '/\[0;36m(.*?)\[0m/s' => '<span class="cyan">$1</span>',
            '/\[0;37m(.*?)\[0m/s' => '<span class="light-gray">$1</span>',

            '/\[1;30m(.*?)\[0m/s' => '<span class="dark-gray">$1</span>',
            '/\[1;31m(.*?)\[0m/s' => '<span class="light-red">$1</span>',
            '/\[1;32m(.*?)\[0m/s' => '<span class="light-green">$1</span>',
            '/\[1;33m(.*?)\[0m/s' => '<span class="yellow">$1</span>',
            '/\[1;34m(.*?)\[0m/s' => '<span class="light-blue">$1</span>',
            '/\[1;35m(.*?)\[0m/s' => '<span class="light-purple">$1</span>',
            '/\[1;36m(.*?)\[0m/s' => '<span class="light-cyan">$1</span>',
            '/\[1;37m(.*?)\[0m/s' => '<span class="white">$1</span>',
        ];

        return preg_replace(array_keys($colors), $colors, $string);
    }

    /** POST page | POST: /
     * @return mixed
     */
    public function store()
    {
        $command = Validator::validate($_REQUEST, ['command' => ['required']])['command'];
        $message = self::convertBash(shell_exec("php " . base_path('terminal') . " $command"));
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
