<?php

namespace zFramework\Core\Facades;

class DB
{
    /**
     * Options parameters
     */
    private $table;
    private $buildQuery = [];
    private $cache = [];
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
        if (!count($args)) $args = $this->cache['buildQuery'] ?? [];
        if (isset($this->observe)) return call_user_func_array([new $this->observe(), 'observer_router'], [$name, array_merge(($this->buildQuery['data'] ?? []), $args)]);
    }

    private function db()
    {
        global $databases, $connected_databases;
        if (!isset($databases[$this->db])) die('Böyle bir veritabanı yok!');
        if (in_array($this->db, $connected_databases)) return $databases[$this->db];

        $connected_databases[] = $this->db;
        return $databases[$this->db] = new \PDO($databases[$this->db][0], $databases[$this->db][1], ($databases[$this->db][2] ?? null));
    }

    // Execute
    public function prepare($sql, $data = [])
    {
        $e = $this->db()->prepare($sql);
        $e->execute($this->buildQuery['data'] ?? ($data ?? []));
        return $e;
    }

    public function table($table)
    {
        $this->table = $table;
        // Table columns
        $this->attributes = $this->prepare("DESCRIBE $this->table")->fetchAll(\PDO::FETCH_COLUMN);
        $this->attrCount = count($this->attributes);
        //

        return $this;
    }

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

    // Query methods
    public function insert(array $data)
    {
        $this->__call(__FUNCTION__);
        if (array_search('created_at', $this->attributes)) $data['created_at'] = time();
        if (array_search('updated_at', $this->attributes)) $data['updated_at'] = time();

        $keys = array_keys($data);

        $insert = $this->prepare("INSERT INTO $this->table(" . implode(', ', $keys) . ") VALUES (:" . implode(', :', $keys) . ")", $data)->rowCount();

        if ($insert) {
            $this->lastID = $this->db()->lastInsertId();
            $this->__call('inserted', [['id' => $this->lastID]]);

            return $this->where('id', '=', $this->lastID)->first();
        }

        throw new \Exception('Can not inserted.');
    }

    public function update(array $sets)
    {
        $this->__call(__FUNCTION__);

        if (array_search('updated_at', $this->attributes)) $sets['updated_at'] = time();

        $sql_set = '';
        foreach ($sets as $key => $_) {
            $sql_set .= "$key = :$key, ";
            $this->buildQuery['data'][$key] = $_;
        }
        $this->buildQuery['sets'] = " SET " . rtrim($sql_set, ', ') . " ";

        $update = $this->run('update')->rowCount() ? true : false;
        $this->__call('updated');

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
            return $this->update([$this->deleted_at => time()]);
        });
        $this->__call('deleted');
        return $delete;
    }
    //

    // SELECT METHODS
    public function first($class = false)
    {
        return self::limit(1)->get($class)[0] ?? null;
    }

    public function firstOrFail()
    {
        $row = call_user_func_array([$this, 'first'], func_get_args()) ?? [];
        if (count((array) $row)) return $row;
        abort(404);
    }

    public function get($class = false)
    {
        $fetch = \PDO::FETCH_ASSOC;
        if ($class) $fetch = \PDO::FETCH_CLASS;
        return self::run()->fetchAll($fetch);
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
            'links' => function () use ($max_page_count, $current_page, $url, $uniqueID) { ?>
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

    // Build
    public function select($select)
    {
        $this->buildQuery['select'] = $select;
        return $this;
    }

    public function where($key, $operator, $value = null, $prev = "AND")
    {
        $replaced_key = str_replace(".", "_", $key);

        if (strlen((string) @$this->buildQuery['where']) == 0) $trim = true;
        @$this->buildQuery['where'] .= " $prev $key $operator " . ($value ? ":$replaced_key" : null);
        if (@$trim) @$this->buildQuery['where'] = ltrim($this->buildQuery['where'], " $prev");

        if (!empty($value)) $this->buildQuery['data'][$replaced_key] = $value;
        return $this;
    }

    private function getWhere()
    {
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

    public function join($type = null, $table = "", $onArray = [])
    {
        if ($type) $type = strtoupper($type);

        if (!in_array($type, [null, 'LEFT', 'OUTER', 'RIGHT', 'FULL'])) throw new \Throwable('This not acceptable join type.');
        $this->buildQuery['joins'][] = ($type ? "$type " : null) . "JOIN $table ON " . $onArray[0] . " " . $onArray[1] . " " . $onArray[2];
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

    private function buildSQL($type = "select")
    {
        switch ($type) {
            case 'select':
                $select = $this->buildQuery['select'] ?? implode(', ', array_diff($this->attributes, $this->guard));
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

        $sql = trim(str_replace(['  '], [' '], "$type $this->table" . @$sets . $this->getJoins() . $this->getWhere() . $this->getOrderBy() . $this->getGroupBy() . $this->getLimit()));
        // echo "$sql <br>";
        return $sql;
    }

    private function resetBuild()
    {
        $this->cache['buildQuery'] = $this->buildQuery;
        $this->buildQuery = []; // reset buildQuery
        return $this;
    }

    private function run($type = "select")
    {
        // init search for softDelete
        $this->isSoftDelete(null, function () {
            if (!isset($this->buildQuery['where']) || !strstr($this->buildQuery['where'], $this->deleted_at)) $this->where($this->deleted_at, 'IS NULL');
        });

        $r = self::prepare(self::buildSQL($type));
        $this->resetBuild();
        return $r;
    }
}
