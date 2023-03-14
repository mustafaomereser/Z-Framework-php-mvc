<?php

namespace zFramework\Core\Traits\DB;

trait RelationShips
{
    public function hasMany($model, $value, $column = null)
    {
        if (!$column) $column = $this->table . "_id";
        return model($model)->where($column, '=', $value);
    }
}
