<?php

namespace App\Models;

use Core\Abstracts\Model;

class User extends Model
{
    public $table = "users";

    public function getAttributes()
    {
        return [$this->attributes, $this->attrCount];
    }
}
