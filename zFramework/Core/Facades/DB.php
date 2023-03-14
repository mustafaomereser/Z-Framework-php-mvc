<?php

namespace zFramework\Core\Facades;

use zFramework\Core\Helpers\Date;

class DB
{
    /**
     * Options parameters
     */
    private $table;
    private $buildQuery = [];
    private $cache = [];
    public $data = null;
    public $lastID = 0;

    public $attributes = [];
    public $attrCount = 0;

    /**
     * Initial, Select Database.
     */
    public function __construct($db = null)
    {
        global $databases;
        if ($db && isset($databases[$db])) $this->db = $db;
        else $this->db = array_keys($databases)[0];
    }

    public function __call(string $name, array $args = [])
    {
        if (!count($args)) $args = $this->cache['buildQuery']['data'] ?? [];
        foreach ($this->buildQuery['data'] ?? [] as $key => $val) $args[$key] = $val;
        if (isset($this->observe)) return call_user_func_array([new $this->observe(), 'router'], [$name, $args]);
    }

    private function db()
    {
        global $connected_databases, $databases;

        if (!isset($databases[$this->db])) die('Böyle bir veritabanı yok!');
        if (in_array($this->db, $connected_databases)) return $databases[$this->db];

        $connected_databases[] = $this->db;
        $parameters = $databases[$this->db];

        // For WebSocket api
        if (gettype($parameters) == 'object') return $databases[$this->db];;

        $databases[$this->db] = new \PDO($parameters[0], $parameters[1], ($parameters[2] ?? null));
        foreach ($parameters['options'] ?? [] as $option) $databases[$this->db]->setAttribute($option[0], $option[1]);

        return $databases[$this->db];
    }

    // Execute
    public function prepare($sql, $data = [])
    {
        $e = $this->db()->prepare($sql);
        $e->execute($this->buildQuery['data'] ?? ($data ?? []));
        $this->data = $e;
        return $e;
    }

    public function tables()
    {
        $dbname = $this->prepare('select database()')->fetchColumn();
        $tables = $this->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = :this_database", ['this_database' => $dbname])->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($tables as $key => $table) $tables[$key] = $table['TABLE_NAME'];
        return $tables;
    }

    public function table($table)
    {
        if (!in_array($table, $this->tables())) throw new \Exception("$table is not exists in tables list.");

        $this->table = $table;
        // Table columns
        $this->attributes = $this->prepare("DESCRIBE $this->table")->fetchAll(\PDO::FETCH_COLUMN);
        $this->attrCount = count($this->attributes);
        //

        return $this;
    }

    // Query methods
    public function insert(array $data)
    {
        $this->resetBuild();
        $this->__call(__FUNCTION__);
        // if (array_search($this->created_at, $this->attributes)) $data[$this->created_at] = Date::timestamp();
        // if (array_search($this->updated_at, $this->attributes)) $data[$this->updated_at] = Date::timestamp();

        $keys = array_keys($data);
        try {
            $insert = $this->prepare("INSERT INTO $this->table(" . implode(', ', $keys) . ") VALUES (:" . implode(', :', $keys) . ")", $data)->rowCount();
            if ($insert) {
                $this->lastID = $this->db()->lastInsertId();
                $this->__call('inserted', [['id' => $this->lastID]]);

                return $this->where('id', '=', $this->lastID)->first();
            }
        } catch (\PDOException $e) {
            throw new \Exception($e->errorInfo[2]);
        }

        throw new \Exception('Can not inserted.');
    }

    public function update(array $sets)
    {
        $this->__call(__FUNCTION__);
        // if (array_search($this->updated_at, $this->attributes)) $sets[$this->updated_at] = Date::timestamp();

        $sql_set = '';
        foreach ($sets as $key => $_) {
            $replaced_key = $key . "_" . uniqid();

            $sql_set .= "$key = :$replaced_key, ";
            $this->buildQuery['data'][$replaced_key] = $_;
        }
        $this->buildQuery['sets'] = " SET " . rtrim($sql_set, ', ') . " ";

        $update = $this->run('update')->rowCount();
        if ($update) $this->__call('updated');
        return $update;
    }

    // Is it soft delete?
    private function isSoftDelete($falseCallback = null, $trueCallback = null)
    {
        if (!isset($this->softDelete) || !@$this->softDelete) return $falseCallback ? $falseCallback() : null;
        elseif (array_search($this->deleted_at, $this->attributes)) return $trueCallback ? $trueCallback() : null;
        else throw new \Exception("Model haven't <b>$this->deleted_at</b> attribute.");
    }

    public function delete()
    {
        $this->__call(__FUNCTION__);
        $delete = $this->isSoftDelete(function () {
            return $this->run('delete') ? true : false;
        }, function () {
            // $this->buildQuery['data']['current'] = Date::timestamp();
            // return $this->prepare("UPDATE $this->table SET $this->deleted_at = :current" . $this->getWhere())->rowCount();
            return $this->update([$this->deleted_at => Date::timestamp()]);
        });
        if ($delete) $this->__call('deleted');
        return $delete;
    }
    //

    // SELECT METHODS
    public function find($find, $class = false, $column_name = null)
    {
        return $this->resetBuild()->where($column_name ?? $this->attributes[0], '=', $find)->first($class);
    }

    public function findSlug($slug)
    {
        $find = $this->find($slug, true, 'slug');
        if (@$find->id) $slug .= rand(0, 100);
        return $slug;
    }


    public function first($class = false)
    {
        return self::limit(1)->get($class)[0] ?? null;
    }

    public function firstOrFail($action = null)
    {
        $row = call_user_func_array([$this, 'first'], func_get_args()) ?? [];
        if (count((array) $row)) return $row;

        if (gettype($action) == 'object') return $action();
        abort(404, $action);
    }

    public function get($class = false)
    {
        $fetch = \PDO::FETCH_ASSOC;
        if ($class) $fetch = \PDO::FETCH_CLASS;

        $get = self::run()->fetchAll($fetch);

        // foreach ($get as $key => $val) { 
        //     $get[$key]['test'] = function () use ($val) {
        //         echo $val['id'];
        //     };
        // }

        return $get;
    }

    public function count()
    {
        return self::run()->rowCount();
    }

    public function againSameQuery()
    {
        if (isset($this->cache['buildQuery'])) $this->buildQuery = $this->cache['buildQuery'];
        return $this;
    }

    public function paginate($per_page_count = 20, $page_request_name = 'page', $class = false)
    {
        $row_count = self::count();

        $uniqueID = uniqid();
        $current_page = (request($page_request_name) ?? 1);
        $max_page_count = ceil($row_count / $per_page_count);

        if ($current_page > $max_page_count) $current_page = $max_page_count;
        elseif ($current_page <= 0) $current_page = 1;


        // Again reload same query
        $this->againSameQuery();
        //

        $start_count = ($per_page_count * ($current_page - 1));
        if (!$row_count) $start_count = -1;

        parse_str(@$_SERVER['QUERY_STRING'], $queryString);
        $queryString[$page_request_name] = "{change_page_$uniqueID}";
        $url = "?" . http_build_query($queryString);

        $return = [
            'items' => $row_count ? self::limit($start_count, $per_page_count)->get($class) : [],
            'item_count' => $row_count,
            'shown' => ($start_count + 1) . " / " . (($per_page_count * $current_page) >= $row_count ? $row_count : ($per_page_count * $current_page)),
            'start' => ($start_count + 1),

            'per_page' => $per_page_count,
            'max_page_count' => $max_page_count,
            'current_page' => $current_page,

            'links' => function ($view) use ($max_page_count, $current_page, $url, $uniqueID) {
                if ($view) return view($view, compact('max_page_count', 'current_page', 'url', 'uniqueID'));
?>
            <ul class="pagination">
                <?php for ($x = 1; $x <= $max_page_count; $x++) : ?>
                    <li class="<?= $x == $current_page ? 'active' : null ?>">
                        <a href="<?= str_replace("%7Bchange_page_$uniqueID%7D", $x, $url) ?>">
                            <?= $x ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
<?php
            }
        ];

        return $class ? (object) $return : $return;
    }

    public function getPrimary()
    {
        if (@$primary = $this->data[$this->primary]) return $primary;
        return new \Exception("This object haven't a primary key.");
    }

    // Build
    public function select($select)
    {
        $this->buildQuery['select'] = $select;
        return $this;
    }

    public function selectAutoBuild()
    {
        $select = "";

        $this->buildQuery['automatic_select_builder'][$this->table] = [
            'as'      => ($this->as ? $this->as : $this->table),
            'columns' => array_diff($this->attributes, ($this->guard ?? []))
        ];

        foreach ($this->buildQuery['automatic_select_builder'] as $table => $data) {
            $as = $data['as'];
            foreach ($data['columns'] as $column) $select .= "$as.$column AS " . $column . "_$as, ";
        }

        $this->buildQuery['select'] = rtrim($select, ', ');

        return $this;
    }


    public function where($key, $operator, $value = null, $prev = "AND")
    {
        $replaced_key = str_replace(".", "_", $key) . "_" . uniqid();

        if (strlen((string) @$this->buildQuery['where']) == 0) $trim = true;
        @$this->buildQuery['where'] .= " $prev " . (!strstr($key, '.') ? ($this->as ? $this->as : $this->table) . "." : null) . "$key $operator " . ($value ? ":$replaced_key" : (string) $value);
        if (@$trim) @$this->buildQuery['where'] = ltrim($this->buildQuery['where'], " $prev");

        if (!empty($value)) $this->buildQuery['data'][$replaced_key] = $value;
        return $this;
    }

    public function whereRaw($sql, $data = [], $prev = "AND")
    {
        // @$this->buildQuery['where'] .= " $sql ";

        $this->buildQuery['data'] = array_merge(($this->buildQuery['data'] ?? []), $data);

        $this->where('', $sql, '', $prev);
        return $this;
    }

    private function getWhere()
    {
        // init search for softDelete
        $this->isSoftDelete(null, function () {
            if (!isset($this->buildQuery['where']) || !strstr($this->buildQuery['where'], $this->deleted_at)) $this->where(($this->as ? $this->as : $this->table) . ".$this->deleted_at", 'IS NULL');
        });

        $where = @$this->buildQuery['where'];
        return $where ? " WHERE $where " : null;
    }

    public function orderBy(array $array)
    {
        $this->buildQuery['orderBy'] = $array;
        return $this;
    }

    private function getOrderBy()
    {
        $orderBy = $this->buildQuery['orderBy'] ?? [];

        if (count($orderBy)) {
            $orderByStr = '';
            foreach ($orderBy as $column => $order) $orderByStr .= "$column $order, ";
            $orderByStr = rtrim($orderByStr, ', ');
            return " ORDER BY $orderByStr ";
        }

        return null;
    }

    public function limit(int $startPoint = 0, $getCount = null)
    {
        $this->buildQuery['limit'] = $startPoint . ($getCount ? ", $getCount" : null);
        return $this;
    }

    private function getLimit()
    {
        $limit = @$this->buildQuery['limit'];
        return $limit ? " LIMIT $limit " : null;
    }

    public function join($type = null, $model = null, $onArray = [])
    {
        if (!$model) new \Exception('Model can not be empty!');
        if ($type) $type = strtoupper($type);

        $model = new $model();
        $table = $model->table;

        if (!in_array($type, [null, 'LEFT', 'LEFT OUTER', 'OUTER', 'RIGHT', 'RIGHT OUTER', 'FULL', 'FULL OUTER ', 'INNER'])) throw new \Throwable('This not acceptable join type.');
        $this->buildQuery['joins'][] = ($type ? "$type " : null) . "JOIN $table" . ($model->as ? " AS $model->as" : null) . " ON " . $onArray[0] . " " . $onArray[1] . " " . $onArray[2];


        // get table's info
        $getinfo = new DB;
        $getinfo = $getinfo->table($table);
        $this->buildQuery['automatic_select_builder'][$table] = [
            'as'      => ($model->as ? $model->as : $table),
            'columns' => array_diff($getinfo->attributes, ($model->guard ?? []))
        ];
        $getinfo = null;
        //

        return $this;
    }

    private function getJoins()
    {
        $joins = $this->buildQuery['joins'] ?? [];

        if (count($joins)) {
            $joinStr = '';
            foreach ($joins as $join) $joinStr .= " $join ";
            return " $joinStr ";
        }

        return null;
    }

    public function groupBy($column)
    {
        $this->buildQuery['groupBy'] = $column;
        return $this;
    }

    private function getGroupBy()
    {
        return @$this->buildQuery['groupBy'] ? " GROUP BY " . $this->buildQuery['groupBy'] : null;
    }

    public function buildSQL($type = "select")
    {
        $as = $this->as ? $this->as : $this->table;

        switch ($type) {
            case 'select':
                $select = $this->buildQuery['select'] ?? ("$as." . implode(", $as.", array_diff($this->attributes, $this->guard ?? [])));
                $type = "SELECT $select FROM";
                break;
            case 'delete':
                $type = "DELETE FROM";
                break;

            case 'update':
                $type = "UPDATE";
                $sets = $this->buildQuery['sets'];
                break;

            default:
                abort(400, 'something wrong, buildSQL invalid type.');
        }

        $sql = trim(str_replace(['  '], [' '], "$type $this->table" . ($as ? " AS $as " : null) . @$sets . $this->getJoins() . $this->getWhere() . $this->getGroupBy() . $this->getOrderBy() . $this->getLimit()));
        // echo "$sql <br>";
        return $sql;
    }

    private function resetBuild()
    {
        $this->cache['buildQuery'] = $this->buildQuery;
        $this->buildQuery = []; // reset buildQuery
        return $this;
    }

    public function reset()
    {
        $this->resetBuild();
        $this->beginQuery();
        return $this;
    }

    private function run($type = "select")
    {
        $r = self::prepare(self::buildSQL($type));
        $this->resetBuild();
        return $r;
    }
}
