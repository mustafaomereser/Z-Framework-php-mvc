<?php

namespace zFramework\Core\Facades;

use ReflectionClass;
use zFramework\Core\Traits\DB\RelationShips;

class DB
{
    use RelationShips;

    private $driver;
    /**
     * Options parameters
     */
    public $table;
    public $buildQuery = [];
    public $cache      = [];

    /**
     * Initial, Select Database.
     * @param string @db
     * @return mixed
     */
    public function __construct(string $db = null)
    {
        global $databases;
        if ($db && isset($databases[$db])) $this->db = $db;
        else $this->db = array_keys($databases)[0];
    }

    /**
     * Create database connection or return already current connection.
     */
    public function db()
    {
        global $connected_databases, $databases;

        if (!isset($databases[$this->db])) die('Böyle bir veritabanı yok!');
        if (!in_array($this->db, $connected_databases)) {
            $connected_databases[] = $this->db;
            $parameters = $databases[$this->db];

            # For WebSocket api
            if (gettype($parameters) == 'object') return $databases[$this->db];

            try {
                $databases[$this->db] = new \PDO($parameters[0], $parameters[1], ($parameters[2] ?? null));
            } catch (\PDOException $err) {
                die(errorHandler($err));
            }

            foreach ($parameters['options'] ?? [] as $option) $databases[$this->db]->setAttribute($option[0], $option[1]);
        }

        $this->driver = $databases[$this->db]->getAttribute(\PDO::ATTR_DRIVER_NAME);
        return $databases[$this->db];
    }

    /**
     * Execute sql query.
     * @param string $sql
     * @param array $data
     * @return array
     */
    public function execute(string $sql, array $data = [])
    {
        $e = $this->db()->prepare($sql);
        $e->execute(count($data) ? $data : $this->buildQuery['data'] ?? []);
        $this->reset();
        return $e;
    }

    /**
     * Select table.
     * @param string $table
     * @return self
     */
    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    #region Preparing
    /**
     * Observer trigger on CRUD methods.
     * @param string $name
     * @param array $args
     * @return mixed
     */
    private function trigger(string $name, array $args = [])
    {
        if (!isset($this->observe)) return false;
        return call_user_func_array([new $this->observe(), 'router'], [$name, $args]);
    }

    /**
     * Reset build.
     * @return self
     */
    private function resetBuild()
    {
        $this->cache['buildQuery'] = $this->buildQuery;
        $this->buildQuery = [
            'select'  => [],
            'join'    => [],
            'where'   => [],
            'orderBy' => [],
            'groupBy' => [],
            'limit'   => [],
            'sets'    => ""
        ];
        return $this;
    }

    /**
     * Model's relatives.
     * @return self
     */
    private function closures()
    {
        $closures = [];
        foreach ((new ReflectionClass($this))->getMethods() as $closure) if (strstr($closure->class, 'Models') && !in_array($closure->name, $this->not_closures)) $closures[] = $closure->name;
        $this->closures = $closures;
        return $this;
    }

    /**
     * Reset all data.
     * @return self
     */
    public function reset()
    {
        $this->resetBuild();
        $this->closures();
        if (method_exists($this, 'beginQuery')) $this->beginQuery();
        return $this;
    }

    /**
     * Emre UZUN was here.
     * Added hash for unique key.
     * @param string $key
     * @return string
     */
    public function hashedKey(string $key): string
    {
        return uniqid(str_replace(".", "_", $key) . "_");
    }
    #endregion

    #region BUILD QUERIES
    /**
     * Set Select
     * @param array $select
     * @return self
     */
    public function select(array $select = [])
    {
        $this->buildQuery['select'] = $select;
        return $this;
    }

    /**
     * Get Select
     * @return null|string
     */
    private function getSelect()
    {
        if (!count($this->buildQuery['select'])) return null;
        return implode(', ', $this->buildQuery['select']);
    }

    /**
     * add a join
     * @param string $type
     * @param string $model
     * @param string $on
     * @return self
     */
    public function join(string $type, string $model, string $on = "")
    {
        $this->buildQuery['join'][] = [$type, $model, $on];
        return $this;
    }

    /**
     * get joins output
     * @return string
     */
    private function getJoin(): string
    {
        $output = "";
        foreach ($this->buildQuery['join'] as $join) {
            $model = new $join[1]();
            $output .= " " . $join[0] . " JOIN $model->table ON " . $join[2] . " ";
        }
        return $output;
    }


    /**
     * add a where
     * @return self
     */
    public function where()
    {
        $parameters = func_get_args();
        if (gettype($parameters[0]) == 'array') {
            $type    = 'group';
            $queries = [];
            foreach ($parameters[0] as $query) {
                $prepare = $this->prepareWhere($query);
                $queries[] = [
                    'key'      => $prepare['key'],
                    'operator' => $prepare['operator'],
                    'value'    => $prepare['value'],
                    'prev'     => $prepare['prev']
                ];
            }
        } else {
            $type    = 'row';
            $prepare = $this->prepareWhere($parameters);
            $queries = [
                [
                    'key'      => $prepare['key'],
                    'operator' => $prepare['operator'],
                    'value'    => $prepare['value'],
                    'prev'     => $prepare['prev']
                ]
            ];
        }

        $this->buildQuery['where'][] = [
            'type'     => $type,
            'queries'  => $queries
        ];

        return $this;
    }

    // public function whereIn($column, $in = [])
    // {
    //   $this->whereIn('sto_kod', [$sto_kod1, $sto_kod2]);
    // }

    /**
     * Prepare where
     * @param array $data
     */
    private function prepareWhere(array $data)
    {
        $key      = $data[0];
        $prev     = "AND";
        $operator = "=";
        $value    = null;

        $count    = count($data);

        if ($count == 2) {
            $value = $data[1];
        } elseif ($count >= 3) {
            $operator = $data[1];
            $value    = $data[2];
        }

        if ($count > 3) $prev = $data[3];

        return compact('key', 'operator', 'value', 'prev');
    }

    /**
     * Parse and get where.
     * @return void|string
     */
    private function getWhere()
    {
        if (!count($this->buildQuery['where'])) return;

        if (isset($this->softDelete)) $this->buildQuery['where'][] = [
            'type'     => 'row',
            'queries'  => [
                [
                    'key'      => $this->deleted_at,
                    'operator' => 'IS NULL',
                    'value'    => null,
                    'prev'     => "AND"
                ]
            ]
        ];

        $output = "";
        foreach ($this->buildQuery['where'] as $where_key => $where) {
            $response = "";
            foreach ($where['queries'] as $query_key => $query) {
                $hashed_key = $this->hashedKey($query['key']);

                if (count($where['queries']) == 1) $prev = ($where_key + $query_key > 0) ? $query['prev'] . " " : null;
                else $prev = ($query_key > 0) ? $query['prev'] . " " : null;

                $response .= $prev . $query['key'] . " " . $query['operator'] . " " . (strlen($query['value']) > 0 ? ":$hashed_key " : null);
                if (strlen($query['value']) > 0) $this->buildQuery['data'][$hashed_key] = $query['value'];
            }

            if ($where['type'] == 'group') $response = $where['queries'][0]['prev'] . " (" . rtrim($response) . ")";
            $output .= $response;
        }

        return " WHERE $output ";
    }

    /**
     * Set Order By
     * @param array $data
     * @return self
     */
    public function orderBy(array $data = [])
    {
        $this->buildQuery['orderBy'] = $data;
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

    /**
     * Set Group By
     * @param array $data
     * @return self
     */
    public function groupBy(array $data = [])
    {
        $this->buildQuery['groupBy'] = $data;
        return $this;
    }


    private function getGroupBy()
    {
        return @$this->buildQuery['groupBy'] ? " GROUP BY " . implode(", ", $this->buildQuery['groupBy']) : null;
    }

    /**
     * Set limit
     * @param int $startPoint
     * @param mixed $getCount
     * @return self
     */
    public function limit(int $startPoint = 0, $getCount = null)
    {
        $this->buildQuery['limit'] = [$startPoint, $getCount];
        return $this;
    }

    private function getLimit()
    {
        $limit = @$this->buildQuery['limit'];
        return $limit ? " LIMIT " . ($limit[0] . ($limit[1] ? ", " . $limit[1] : null)) : null;
    }
    #endregion

    #region CRUD Proccesses

    /**
     * get rows with query string
     * @return array
     */
    public function get()
    {
        $rows = $this->run()->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $key => $row) foreach ($this->closures as $closure) $rows[$key][$closure] = function () use ($row, $closure) {
            return $this->{$closure}($row);
        };

        return $rows;
    }

    /**
     * get one row in rows
     * @return array 
     */
    public function first()
    {
        return $this->limit(1)->get()[0] ?? [];
    }

    /**
     * paginate
     * @param int $per_count
     * @param string $page_name
     * @return array
     */
    public function paginate($per_count = 20, $page_name = 'page')
    {
        $rows      = $this->get();
        $row_count = count($rows);

        $uniqueID = uniqid();
        $current_page = (request($page_name) ?? 1);
        $max_page_count = ceil($row_count / $per_count);

        if ($current_page > $max_page_count) $current_page = $max_page_count;
        elseif ($current_page <= 0) $current_page = 1;

        $start_count = ($per_count * ($current_page - 1));
        if (!$row_count) $start_count = -1;

        parse_str(@$_SERVER['QUERY_STRING'], $queryString);
        $queryString[$page_name] = "{change_page_$uniqueID}";
        $url = "?" . http_build_query($queryString);

        $return = [
            'items'          => $row_count ? array_slice($rows, $start_count, $per_count, true) : [],
            'item_count'     => $row_count,
            'shown'          => ($start_count + 1) . " / " . (($per_count * $current_page) >= $row_count ? $row_count : ($per_count * $current_page)),
            'start'          => ($start_count + 1),

            'per_page'       => $per_count,
            'max_page_count' => $max_page_count,
            'current_page'   => $current_page,

            'links'          => function ($view = null) use ($max_page_count, $current_page, $url, $uniqueID) {
                if (!$view) $view = 'layouts.pagination.default';
                return view($view, compact('max_page_count', 'current_page', 'url', 'uniqueID'));
            }
        ];

        return $return;
    }

    /**
     * Insert a row to database
     * @param array $sets
     * @return self
     */
    public function insert(array $sets = [])
    {
        $this->resetBuild();

        $hashed_keys = [];
        foreach ($sets as $key => $val) {
            $hashed_key =  $this->hashedKey($key);
            $hashed_keys[] = $hashed_key;
            $this->buildQuery['data'][$hashed_key] = $val;
        }

        $this->buildQuery['sets'] = " (" . implode(', ', array_keys($sets)) . ") VALUES (:" . implode(', :', $hashed_keys) . ") ";

        $this->trigger('insert', $sets);
        $insert = $this->run(__FUNCTION__);
        if ($insert) $this->trigger('inserted', $this->resetBuild()->where('id', $this->db()->lastInsertId())->first() ?? []);

        return $insert;
    }

    /**
     * Update row(s) in database
     * @param array $sets
     * @return self
     */
    public function update(array $sets = [])
    {
        $this->buildQuery['sets'] = " SET ";
        foreach ($sets as $key => $val) {
            $hashed_key = $this->hashedKey($key);
            $this->buildQuery['data'][$hashed_key] = $val;
            $this->buildQuery['sets'] .= "$key = :$hashed_key, ";
        }
        $this->buildQuery['sets'] = rtrim($this->buildQuery['sets'], ', ');

        $this->trigger('update');
        $update = $this->run(__FUNCTION__);
        if ($update) $this->trigger('updated');

        return $update;
    }

    /**
     * Delete row(s) in database
     * @return self
     */
    public function delete()
    {
        $this->trigger('delete');
        if (!isset($this->softDelete)) $delete = $this->run(__FUNCTION__);
        else $delete = $this->update([$this->deleted_at => date('Y-m-d H:i:s')]);
        $this->trigger('deleted');

        return $delete;
    }
    #endregion

    #region BUILD & Execute
    public function buildSQL($type = 'select')
    {
        $limit = $this->getLimit();
        switch ($type) {
            case 'select':
                $select = $this->getSelect() ?? '*'; # ?? implode(", ", array_diff($this->columns, $this->guard ?? []));
                $type = "SELECT $select FROM";
                break;

            case 'delete':
                $type = "DELETE FROM";
                break;

            case 'insert':
                $type = "INSERT INTO";
                $sets = $this->buildQuery['sets'];
                break;

            case 'update':
                $type = "UPDATE";
                $sets = $this->buildQuery['sets'];
                break;

            default:
                throw new \Exception('something wrong, buildSQL invalid type.');
        }

        $sql = "$type $this->table" . @$sets . $this->getJoin() . $this->getWhere() . $this->getGroupBy() . $this->getOrderBy() . $limit;
        return $sql;
    }

    public function run($type = 'select')
    {
        return $this->execute($this->buildSQL($type));
    }
    #endregion
}
