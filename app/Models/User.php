<?php

namespace App\Models;

use App\Observers\UserObserver;
use zFramework\Core\Abstracts\Model;
use zFramework\Core\Traits\softDelete;

class User extends Model
{
    use softDelete;

    public $observe = UserObserver::class;

    public $table = "users";
    public $as    = "users_table";
    public $guard = ['password', 'api_token', 'deleted_at', 'created_at'];


    // public function beginQuery()
    // {
    //     return $this->where('id', '=', 1);
    // }

    public function friends()
    {
        return $this->getPrimary();
    }

    public function getAttributes()
    {
        return [$this->attributes, $this->attrCount];
    }
}
