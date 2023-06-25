<?php

namespace zFramework\Core\Abstracts;

use zFramework\Core\Facades\Auth;
use zFramework\Core\Validator;

abstract class Request
{
    public $authorize = false;

    public function validated()
    {
        if ($this->authorize && !Auth::check()) abort(401);
        return Validator::validate($_REQUEST, $this->columns());
    }
}
