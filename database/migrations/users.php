<?php

namespace Database\Migrations;

class Users {

    public $table = "users";
    public $db = 'local';

    public function up() {
        return [
            'id' => ['primary']
        ];
    }
}