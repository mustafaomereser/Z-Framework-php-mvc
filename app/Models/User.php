<?php

namespace App\Models;

use Core\Abstracts\Model;
use Core\Traits\softDelete;

class User extends Model
{
    use softDelete;

    public $table = "users";

    public function getAttributes()
    {
        return [$this->attributes, $this->attrCount];
    }
}
