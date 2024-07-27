<?php

namespace App\Requests\Auth;

use zFramework\Core\Abstracts\Request;

class SigninRequest extends Request
{

    public function __construct()
    {
        $this->authorize      = false;
        $this->htmlencode     = false;
        $this->attributeNames = [];
    }

    public function columns(): array
    {
        return [
            'email'          => ['required', 'email'],
            'password'       => ['required'],
            'keep-logged-in' => ['nullable']
        ];
    }
}
