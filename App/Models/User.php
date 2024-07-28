<?php

namespace App\Models;

use zFramework\Core\Abstracts\Model;
use zFramework\Core\Traits\DB\softDelete;

class User extends Model
{
    use softDelete;

    // public $observe      = [UserObserver::class, TestObserver::class];
    /**
     * Observer key to receive return data
     */
    // public $observe_key  = 0;

    public $table    = "users";
    // public $guard    = ['password', 'api_token'];

    # every reset after begin query.
    // public function beginQuery()
    // {
    //     return $this->where('id', 1);
    // }

    /**
     * every row get this special methods.
     * 
     * using example:
     * *****
     *   $users = (new User)->get();
     *   foreach ($users as $user) $user['posts']()->get();
     * *****
     */
    public function posts($values)
    {
        return $this->hasMany(Posts::class, $values['id'], 'user_id');
    }
}
