<?php

namespace zFramework\Core\Abstracts;

use zFramework\Core\Facades\DB;

abstract class Model extends DB
{
    /**
     * Usual Parameters for organize.
     */
    public $primary      = null;
    public $as           = "";
    public $guard        = [];
    public $closures     = [];
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $not_closures  = ['beginQuery'];
    public $_not_found    = 'Not found.';

    /**
     * Run parent construct and set table.
     */
    public function __construct()
    {
        // $this->created_at = 'created_at';
        // $this->updated_at = 'updated_at';
        // $this->deleted_at = 'deleted_at';
        foreach (config('model.consts') as $key => $val) $this->{$key} = $val;

        parent::__construct(@$this->db);
        parent::table($this->table);
    }
}
