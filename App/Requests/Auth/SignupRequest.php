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

    public function columns($parameter1 = null): array
    {
        return [
            // uses parameters for example
            // 'username'  => ['required', "exists:users ex:$parameter1"],
            'username'  => ['required', 'unique:users'],
            'email'     => ['required', 'email', 'unique:users'],
            'password'  => ['type:string', 'required', 'min:8', 'same:re-password'],
            'terms'     => ['required']
        ];
    }
}
