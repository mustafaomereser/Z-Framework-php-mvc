<?php

namespace Core\Abstracts;

use Core\Facedas\DB;

abstract class Model extends DB
{
    public function __construct()
    {
        parent::__construct($this->db ?? null);
        parent::table($this->table);
    }
}
