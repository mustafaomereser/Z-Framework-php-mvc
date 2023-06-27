<?php

namespace zFramework\Core\Facades;

use zFramework\Core\Helpers\Date;
use zFramework\Core\Traits\DB\RelationShips;

class DB
{
    use RelationShips;

    private $driver;
    /**
     * Options parameters
     */
    private $table;
    private $buildQuery = [];
    private $cache = [];
    private $queue = [
        'mode' => 0,
        'sql'  => [],
        'data' => []
    ];
    public $data   = null;
    public $lastID = 0;

    public $attributes = [];
    public $attrCount  = 0;

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

    public function db()
    {
        global $connected_databases, $databases;

        if (!isset($databases[$this->db])) die('Böyle bir veritabanı yok!');
        if (in_array($this->db, $connected_databases)) return $databases[$this->db];

        $connected_databases[] = $this->db;
        $parameters = $databases[$this->db];

        // For WebSocket api
        if (gettype($parameters) == 'object') return $databases[$this->db];

        try {
            $databases[$this->db] = new \PDO($parameters[0], $parameters[1], ($parameters[2] ?? null));
        } catch (\PDOException $err) {
            die(errorHandler($err));
        }

        foreach ($parameters['options'] ?? [] as $option) $databases[$this->db]->setAttribute($option[0], $option[1]);

        $this->driver = $databases[$this->db]->getAttribute(\PDO::ATTR_DRIVER_NAME);

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
        if (@$tables = $GLOBALS['DB_TABLES'][$GLOBALS["DB_NAMES"][$this->db]]) return $tables;
        try {
            $dbname = $this->prepare('SELECT DATABASE()')->fetchColumn();

            $engines = [];
            $tables  = $this->prepare("SELECT TABLE_NAME, ENGINE FROM information_schema.tables WHERE table_schema = :this_database", ['this_database' => $dbname])->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($tables as $key => $table) {
                $tables[$key] = $table['TABLE_NAME'];
                $engines[$table['TABLE_NAME']] = $table['ENGINE'];
            }

            $GLOBALS["DB_TABLES"][$dbname]         = $tables;
            $GLOBALS["DB_TABLE_ENGINES"][$dbname]  = $engines;
            $GLOBALS["DB_NAMES"][$this->db]        = $dbname;
            return $tables;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function table($table)
    {
        $tables = $this->tables();
        if (is_array($tables) && !strstr($table, ' ')) {
            if (!in_array($table, $tables)) throw new \Exception("$table is not exists in tables list.");
            // Table columns
            $this->attributes = $GLOBALS['DB_DESCRIBES'][$this->db][$table] ?? $this->prepare("DESCRIBE $table")->fetchAll(\PDO::FETCH_COLUMN);
            $this->attrCount  = count($this->attributes);

            $GLOBALS['DB_DESCRIBES'][$this->db][$table] = $this->attributes;
        }

        $this->table = $table;

        return $this;
    }

    // Query methods
    public function insert(array $data)
    {
        $call = $this->__call(__FUNCTION__, $data);

        if (!empty($call)) $data = $call;

        try {
            $sql_sets = [];
            $sql_keys = [];
            foreach ($data as $key => $_) {
                $replaced_key = $key . "_" . uniqid();
                $sql_keys[] = $key;
                $sql_sets[] = $replaced_key;
                #
                if (gettype($_) == 'array') $_ = json_encode($_, JSON_UNESCAPED_UNICODE);
                $this->buildQuery['data'][$replaced_key] = $_;
                #
            }

            $this->buildQuery['sets'] = "(" . implode(', ', $sql_keys) . ") VALUES (:" . implode(', :', $sql_sets) . ")";

            $insert = $this->run('insert');

            if (!$this->queue['mode']) {
                $insert = $insert->rowCount();
                if ($insert) {
                    $this->lastID = $this->db()->lastInsertId();
                    $this->__call('inserted', ['id' => $this->lastID]);
                }
                return in_array($this->primary, $this->attributes) && $insert ? $this->where($this->primary, '=', $this->lastID)->first() : false;
            }
        } catch (\PDOException $e) {
            throw new \Exception($e->errorInfo[2]);
        }

        return false;
        // throw new \Exception('Can not inserted.');
    }

    public function update(array $sets)
    {
        $call = $this->__call(__FUNCTION__, $sets);
        if (!empty($call)) $sets = $call;

        $sql_sets = [];
        foreach ($sets as $key => $_) {
            $replaced_key = $key . "_" . uniqid();

            $sql_sets[] = "$key = :$replaced_key";

            #
            if (gettype($_) == 'array') $_ = json_encode($_, JSON_UNESCAPED_UNICODE);
            $this->buildQuery['data'][$replaced_key] = $_;
            #
        }
        $this->buildQuery['sets'] = " SET " . implode(', ', $sql_sets) . " ";

        $update = $this->run('update');
        if (!$this->queue['mode']) {
            $update = $update->rowCount();
            if ($update) $this->__call('updated', ['where' => $this->buildQuery['where'] ?? [], 'sets' => $sets]);
            return $update;
        }
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
        # feed observe
        $observe_data = $this->buildQuery['where'] ?? [];
        #
        $this->__call(__FUNCTION__, $observe_data);
        $delete = $this->isSoftDelete(function () {
            return $this->run('delete') ? true : false;
        }, function () {
            return $this->update([$this->deleted_at => Date::timestamp()]);
        });
        if ($delete) $this->__call('deleted', array_merge($observe_data, ['softDelete' => $this->softDelete]));
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

        if (count($this->closures)) foreach ($get as $key => $val) foreach ($this->closures as $name => $closure) {
            $closure = function () use ($val, $closure) {
                return $closure($val);
            };

            if ($class) $get[$key]->$name = $closure;
            else $get[$key][$name] = $closure;
        }


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


        # Again reload same query
        $this->againSameQuery();


        $start_count = ($per_page_count * ($current_page - 1));
        if (!$row_count) $start_count = -1;

        parse_str(@$_SERVER['QUERY_STRING'], $queryString);
        $queryString[$page_request_name] = "{change_page_$uniqueID}";
        $url = "?" . http_build_query($queryString);

        $return = [
            'items'          => $row_count ? self::limit($start_count, $per_page_count)->get($class) : [],
            'item_count'     => $row_count,
            'shown'          => ($start_count + 1) . " / " . (($per_page_count * $current_page) >= $row_count ? $row_count : ($per_page_count * $current_page)),
            'start'          => ($start_count + 1),

            'per_page'       => $per_page_count,
            'max_page_count' => $max_page_count,
            'current_page'   => $current_page,

            'links'          => function ($view = null) use ($max_page_count, $current_page, $url, $uniqueID) {
                if (!$view) $view = 'layouts.pagination.default';
                return view($view, compact('max_page_count', 'current_page', 'url', 'uniqueID'));
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

    private function getWhere($type = null)
    {
        if ($type == 'insert') return null;

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
        // $this->buildQuery['limit'] = $startPoint . ($getCount ? ", $getCount" : null);
        $this->buildQuery['limit'] = [$startPoint, $getCount];
        return $this;
    }

    private function getLimit()
    {
        $limit = @$this->buildQuery['limit'];
        switch ($this->driver) {
            case 'mysql':
                return $limit ? " LIMIT " . ($limit[0] . ($limit[1] ? ", " . $limit[1] : null)) : null;
                break;

            case 'sqlsrv':
                if (!$this->buildQuery['orderBy']) $this->buildQuery['orderBy'] = ['id' => ''];
                return $limit ? " OFFSET " . (is_null($limit[1]) ? 0 : $limit[0]) . " ROWS FETCH NEXT " . (is_null($limit[1]) ? $limit[0] : $limit[1]) . " ROWS ONLY" : null;
                break;
        }
    }

    public function join($type = null, $model = null, $on = [])
    {
        if (!$model) new \Exception('Model can not be empty!');
        if ($type) $type = strtoupper($type);

        if (gettype($on) === 'array') $on = implode(' ', $on);

        $model = new $model();
        $table = $model->table;

        if (!in_array($type, [null, 'LEFT', 'LEFT OUTER', 'OUTER', 'RIGHT', 'RIGHT OUTER', 'FULL', 'FULL OUTER ', 'INNER'])) throw new \Throwable('This not acceptable join type.');
        $this->buildQuery['joins'][] = ($type ? "$type " : null) . "JOIN $table" . ($model->as ? " AS $model->as" : null) . " ON $on";


        // get table's info
        $getinfo = (new DB)->table($table);
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

    public function buildSQL($select_type = "select")
    {
        $as = $this->as ? $this->as : $this->table;

        switch ($select_type) {
            case 'select':
                $select = $this->buildQuery['select'] ?? ("$as." . implode(", $as.", array_diff($this->attributes, $this->guard ?? [])));
                $type = "SELECT $select FROM";
                break;

            case 'delete':
                $type = "DELETE FROM";
                break;

            case 'insert':
                $type = "INSERT INTO";
                $as   = null;
                $sets = $this->buildQuery['sets'];
                break;

            case 'update':
                $type = "UPDATE";
                $sets = $this->buildQuery['sets'];
                break;

            default:
                abort(400, 'something wrong, buildSQL invalid type.');
        }

        $getLimit = $this->getLimit();
        $sql      = trim(str_replace(['  '], [' '], "$type $this->table" . ($as ? " AS $as " : null) . @$sets . $this->getJoins() . $this->getWhere($select_type) . $this->getGroupBy() . $this->getOrderBy() . $getLimit));
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
        $this->closures();
        return $this;
    }

    private function run($type = "select")
    {
        $sql = self::buildSQL($type);

        $queue_mode = $this->queue['mode'] && $type != 'select';

        if (!$queue_mode) $result = self::prepare($sql);
        $this->resetBuild();
        if ($queue_mode) {
            $this->queue['sql'][] = $sql;
            $this->queue['data'] = array_merge($this->cache['buildQuery']['data'] ?? [], $this->queue['data']);
            $result = 'queued';
        }

        return $result;
    }

    public function queue()
    {
        $this->queue['mode'] = $this->queue['mode'] ? 0 : 1;
        if ($this->queue['mode']) {
            $this->queue['sql']  = [];
            $this->queue['data'] = [];
            return 'queue mode on';
        } else {
            if (!count($this->queue['sql'])) return false;
            $result = $this->prepare(implode(';', $this->queue['sql']), $this->queue['data'])->rowCount();
            return $result;
        }
    }

    # Transaction
    private function checkisInnoDB()
    {
        if (empty($this->table)) throw new \Exception('This table is not defined.');
        if ($GLOBALS['DB_TABLE_ENGINES'][$GLOBALS["DB_NAMES"][$this->db]][$this->table] == 'InnoDB') return true;
        throw new \Exception('This table is not InnoDB. If you want to use transaction system change store engine to InnoDB.');
    }

    public function beginTransaction()
    {
        $this->checkisInnoDB();

        $this->db()->beginTransaction();
        return $this;
    }

    public function rollback()
    {
        $this->db()->rollBack();
        return $this;
    }

    public function commit()
    {
        $this->db()->commit();
        return $this;
    }
    #
}
