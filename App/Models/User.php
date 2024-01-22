<?php

namespace App\Models;

use App\Observers\UserObserver;
use zFramework\Core\Abstracts\Model;
use zFramework\Core\Traits\DB\softDelete;

class User extends Model
{
    use softDelete;

    public $observe  = UserObserver::class;

    public $table    = "users";
    public $guard    = ['password', 'api_token'];

    # everytime set query begin.
    // public function beginQuery()
    // {
    //     return $this->where('id', 1);
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
    public function posts($values)
    {
        return $this->hasMany(Posts::class, $values['id'], 'user_id');
    }
}
