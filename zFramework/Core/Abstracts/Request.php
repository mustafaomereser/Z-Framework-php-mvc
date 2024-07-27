<?php

namespace zFramework\Core\Abstracts;

use zFramework\Core\Facades\Auth;
use zFramework\Core\Validator;

abstract class Request
{
    public $authorize      = false;
    public $htmlencode     = false;
    public $attributeNames = [];

    public function columns(): array
    {
        return [];
    }

    /**
     * Validate from extends.
     * @return array
     */
    public function validated(): array
    {
        if ($this->authorize && !Auth::check()) abort(401);
        $validate = Validator::validate($_REQUEST, $this->columns(func_get_args()), $this->attributeNames);
        if ($this->htmlencode) foreach ($validate as $key => $val) if (gettype($val) == 'string') $validate[$key] = htmlspecialchars($val);
        return $validate;
    }
}
