<?php

namespace zFramework\Core\Abstracts;

use zFramework\Core\Facedas\DB;

abstract class Model extends DB
{
    /**
     * Usual Parameters for organize.
     */
    public $primary = "id";
    public $created_at = 'created_at';
    public $updated_at = 'updated_at';
    public $deleted_at = 'deleted_at';

    /**
     * Run parent construct and set table.
     */
    public function __construct()
    {
        parent::__construct($this->db ?? null);
        parent::table($this->table);
    }
}
