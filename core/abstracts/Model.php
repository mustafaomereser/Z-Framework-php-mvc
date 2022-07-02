<?php

namespace Core\Abstracts;

use Core\Facedas\DB;

abstract class Model extends DB
{
    public $primary = "id";
    public $created_at = 'created_at';
    public $updated_at = 'updated_at';
    public $deleted_at = 'deleted_at';

    public function __construct()
    {
        parent::__construct($this->db ?? null);
        parent::table($this->table);
    }
}
