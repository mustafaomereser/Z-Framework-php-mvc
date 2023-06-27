<?php

namespace App\Models;

use App\Observers\UserObserver;
use zFramework\Core\Abstracts\Model;
use zFramework\Core\Traits\softDelete;

class User extends Model
{
    use softDelete;

    // public $observe  = UserObserver::class;

    public $table    = "users";
    public $as       = "users_table";
    public $guard    = ['password', 'api_token', 'deleted_at', 'created_at'];

    # everytime set query begin.
    // public function beginQuery()
    // {
    //     return $this->where('id', '=', 1);
    // }

    /**
     * every row get this special methods.
     * 
     * example:
     * *****
     *   $users = (new User)->get();
     *   foreach ($users as $user) $user['posts']()->get();
     * *****
     */
    public function closures()
    {
        $this->closures = [
            'posts' => function ($values) {
                return $this->hasMany(Posts::class, $values['id'], 'user_id');
            }
        ];
    }

    public function friends()
    {
        return $this->getPrimary();
    }

    public function getAttributes()
    {
        return [$this->attributes, $this->attrCount];
    }
}
