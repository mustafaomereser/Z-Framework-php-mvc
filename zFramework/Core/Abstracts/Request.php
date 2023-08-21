<?php

namespace zFramework\Core\Abstracts;

use zFramework\Core\Facades\Auth;
use zFramework\Core\Validator;

abstract class Request
{
    public $authorize = false;

    /**
     * Validate from extends.
     * @param array $attributeNames
     * @return array
     */
    public function validated(array $attributeNames = [], bool $htmlspecialchars = false)
    {
        if ($this->authorize && !Auth::check()) abort(401);
        $validate = Validator::validate($_REQUEST, $this->columns(), $attributeNames);
        if ($htmlspecialchars) foreach ($validate as $key => $val) if (gettype($val) == 'string') $validate[$key] = htmlspecialchars($val);
        return $validate;
    }
}
