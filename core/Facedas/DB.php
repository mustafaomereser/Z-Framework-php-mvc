<?php

namespace Core\Facedas;

class DB
{
    private $table;
    private $buildQuery = [];

    public $lastID = 0;
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
        return $databases[$this->db] = new \PDO($databases[$this->db][0], $databases[$this->db][1], $databases[$this->db][2]);
    }

    public function prepare($sql, $data = [])
    {
        $e = $this->db()->prepare($sql);
        $e->execute($data);
        return $e;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function update(array $sets)
    {
        $sql_set = '';
        foreach ($sets as $key => $_) {
            $sql_set .= "$key = :$key, ";
            $this->buildQuery['data'][$key] = $_;
        }
        $sql_set = rtrim($sql_set, ', ');

        $update = self::prepare("UPDATE $this->table SET $sql_set" . $this->getWhere(), $this->buildQuery['data'] ?? []);
        if ($update) return true;

        abort(500);
    }

    public function insert(array $data)
    {

        $keys = array_keys($data);
        $insert = $this->prepare("INSERT INTO $this->table(" . implode(', ', $keys) . ") VALUES (:" . implode(', :', $keys) . ")", $data)->rowCount();

        if ($insert) {
            $this->lastID = $this->db()->lastInsertId();
            return $this->prepare("SELECT * FROM $this->table WHERE id = " . $this->lastID)->fetch(\PDO::FETCH_ASSOC);
        }

        abort(500);
    }

    public function first()
    {
        return self::run()->fetch(\PDO::FETCH_ASSOC);
    }

    public function get()
    {
        return self::run()->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function count()
    {
        return self::run()->rowCount();
    }

    // Build
    public function select($select)
    {
        $this->buildQuery['select'] = $select;
        return $this;
    }

    public function where($key, $operator, $value, $prev = "AND")
    {
        if (strlen(@$this->buildQuery['where']) == 0) $trim = true;
        @$this->buildQuery['where'] .= " $prev $key $operator :$key";
        if (@$trim) @$this->buildQuery['where'] = ltrim($this->buildQuery['where'], " $prev");

        $this->buildQuery['data'][$key] = $value;
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

    public function buildSQL()
    {
        $select = $this->buildQuery['select'] ?? '*';
        $sql = trim(str_replace(['  '], [' '], "SELECT $select FROM $this->table" . $this->getJoins() . $this->getWhere() . $this->getOrderBy() . $this->getLimit()));
        return $sql;
    }

    private function run()
    {
        $r = self::prepare(self::buildSQL(), $this->buildQuery['data'] ?? []);
        $this->buildQuery = []; // reset buildQuery
        return $r;
    }
}
