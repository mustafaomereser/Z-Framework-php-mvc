<?php

namespace Database\Seeders;

use App\Models\User;
use zFramework\Core\Crypter;
use zFramework\Core\Facades\Str;

class Seeder
{
    public function __construct()
    {
        $this->user = new User;
    }

    public function seed()
    {
        $this->user->insert([
            'username' => 'admin',
            'password' => Crypter::encode('admin'),
            'email' => 'admin@localhost.com',
            'api_token' => Str::rand(60)
        ]); 
    }
}
