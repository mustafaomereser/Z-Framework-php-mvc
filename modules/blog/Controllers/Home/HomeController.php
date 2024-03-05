<?php

namespace Modules\Blog\Controllers\Home;

use zFramework\Core\Abstracts\Controller;

#[\AllowDynamicProperties]
class HomeController extends Controller
{

    public function __construct()
    {
        // Set models here (suggestion)
        // $this->user = new User();
        // $this->user->where('id', '=', 1)->first();
    }

    public function home()
    {
        return view('modules.blog.views.pages.home');
    }
}
