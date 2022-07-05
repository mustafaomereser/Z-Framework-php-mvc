<?php

namespace App\Models;

use zFramework\Core\Abstracts\Model;
use zFramework\Core\Traits\softDelete;

class User extends Model
{
    use softDelete;

    public $table = "users";
    public $guard = ['password', 'api_token', 'deleted_at', 'created_at'];

    public function getAttributes()
    {
        return [$this->attributes, $this->attrCount];
    }
}
