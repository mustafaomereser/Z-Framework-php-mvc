<?php

namespace zFramework\Core\Facades;

class DBColumns
{
    public static function columnsLength($model)
    {
        $model = new $model;
        $return = [];
        $list = (new DB($model->db))->prepare("SELECT COLUMN_NAME, CHARACTER_MAXIMUM_LENGTH FROM information_schema.columns where table_schema = DATABASE() AND table_name = '$model->table'")->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($list as $item) $return[$item['COLUMN_NAME']] = $item['CHARACTER_MAXIMUM_LENGTH'];
        return $return;
    }
}
