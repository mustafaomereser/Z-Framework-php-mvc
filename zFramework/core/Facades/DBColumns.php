<?php

namespace zFramework\Core\Facades;

class DBColumns
{
    public static function columnsMaxLen($model)
    {
        $model = new $model;
        $db = new DB($model->db);
        $r = [];
        $list = $db->prepare("SELECT COLUMN_NAME, CHARACTER_MAXIMUM_LENGTH FROM information_schema.columns where table_schema = DATABASE() AND table_name = '$model->table'")->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($list as $item) $r[$item['COLUMN_NAME']] = $item['CHARACTER_MAXIMUM_LENGTH'];
        return $r;
    }
}
