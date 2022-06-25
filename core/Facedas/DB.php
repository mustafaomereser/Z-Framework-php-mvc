<?php

namespace Core\Facedas;

class DB
{
    private $table;
    private $buildQuery = [];
    private $cache = [];
    public $lastID = 0;

    public $attributes = [];
    public $attrCount = 0;

    public function __construct($db = null)
    {
        global $databases;
        if ($db && isset($databases[$db]))
            $this->db = $db;
        else
            $this->db = array_keys($databases)[0];
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
        if (array_search('created_at', $this->attributes)) $data['created_at'] = time();
        if (array_search('updated_at', $this->attributes)) $data['updated_at'] = time();

        $keys = array_keys($data);

        $insert = $this->prepare("INSERT INTO $this->table(" . implode(', ', $keys) . ") VALUES (:" . implode(', :', $keys) . ")", $data)->rowCount();

        if ($insert) {
            $this->lastID = $this->db()->lastInsertId();
            return $this->prepare("SELECT * FROM $this->table WHERE id = " . $this->lastID)->fetch(\PDO::FETCH_ASSOC);
        }

        abort(500);
    }

    public function update(array $sets)
    {
        if (array_search('updated_at', $this->attributes)) $sets['updated_at'] = time();

        $sql_set = '';
        foreach ($sets as $key => $_) {
            $sql_set .= "$key = :$key, ";
            $this->buildQuery['data'][$key] = $_;
        }
        $sql_set = rtrim($sql_set, ', ');

        $update = self::prepare("UPDATE $this->table SET $sql_set" . $this->getWhere())->rowCount();
        return $update ? true : false;
    }

    public function delete()
    {
        $delete = self::prepare("DELETE FROM $this->table" . $this->getWhere())->rowCount();
        return $delete ? true : false;
    }

    // SELECT METHODS
    public function first($class = false)
    {
        return self::limit(1)->get($class)[0] ?? null;
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

    public function paginate($per_page_count = 20, $page_request_name = 'page', $class = false)
    {
        $uniqueID = uniqid();

        $current_page = (request($page_request_name) ?? 1);
        $row_count = self::count();
        $max_page_count = ceil($row_count / $per_page_count);

        if ($current_page > $max_page_count)
            $current_page = $max_page_count;
        elseif ($current_page <= 0)
            $current_page = 1;


        // Again reload same query
        $this->buildQuery = $this->cache['buildQuery'];
        //

        $start_count = ($per_page_count * ($current_page - 1));

        parse_str(@$_SERVER['QUERY_STRING'], $queryString);
        $queryString[$page_request_name] = "{change_page_$uniqueID}";
        $url = "?" . http_build_query($queryString);


        $return = [
            'items' => self::limit($start_count, $per_page_count)->get($class),
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

    public function where($key, $operator, $value, $prev = "AND")
    {
        $replaced_key = str_replace(".", "_", $key);

        if (strlen(@$this->buildQuery['where']) == 0) $trim = true;
        @$this->buildQuery['where'] .= " $prev $key $operator :$replaced_key";
        if (@$trim) @$this->buildQuery['where'] = ltrim($this->buildQuery['where'], " $prev");

        $this->buildQuery['data'][$replaced_key] = $value;
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

        if (!in_array($type, [null, 'LEFT', 'OUTER', 'RIGHT', 'FULL'])) abort(500, 'This not acceptable join type.');
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

    private function buildSQL()
    {
        $select = $this->buildQuery['select'] ?? '*';
        $sql = trim(str_replace(['  '], [' '], "SELECT $select FROM $this->table" . $this->getJoins() . $this->getWhere() . $this->getOrderBy() . $this->getGroupBy() . $this->getLimit()));
        return $sql;
    }

    private function resetBuild()
    {
        $this->cache['buildQuery'] = $this->buildQuery;
        $this->buildQuery = []; // reset buildQuery

        return $this;
    }

    private function run()
    {
        $r = self::prepare(self::buildSQL());
        $this->resetBuild();
        return $r;
    }
}
