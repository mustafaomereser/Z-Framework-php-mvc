<?php

namespace App\Requests\Auth;

use zFramework\Core\Abstracts\Request;

class SignupRequest extends Request
{

    public function __construct()
    {
        $this->authorize      = false;
        $this->htmlencode     = true;
        $this->attributeNames = [];
    }

    public function columns()
    {
        return [
            'username'  => ['required', 'unique:users'],
            'email'     => ['required', 'email', 'unique:users'],
            'password'  => ['type:string', 'required', 'min:8', 'same:re-password'],
            'terms'     => ['required']
        ];
    }
}
