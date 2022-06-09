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
        $where = @$this->buildQuery['where'];

        foreach ($sets as $key => $_) {
            $sql_set .= "$key = :$key, ";
            $this->buildQuery['data'][$key] = $_;
        }
        $sql_set = rtrim($sql_set, ', ');

        $update = self::prepare("UPDATE $this->table SET $sql_set" . ($where ? " WHERE $where" : null), $this->buildQuery['data'] ?? []);
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

    public function select($select)
    {
        $this->buildQuery['select'] = $select;
        return $this;
    }

    public function where($key, $operator, $value, $prev = "AND")
    {
        if (strlen(@$this->buildQuery['where']) == 0) $trim = true;
        @$this->buildQuery['where'] .= " $prev $key $operator :$key";
        if (@$trim) @$this->buildQuery['where'] = substr($this->buildQuery['where'], (strlen($prev) + 1));

        $this->buildQuery['data'][$key] = $value;
        return $this;
    }

    public function buildSQL()
    {
        $select = $this->buildQuery['select'] ?? '*';
        $where = @$this->buildQuery['where'];

        $sql = "SELECT $select FROM $this->table" . ($where ? " WHERE $where" : null);
        return $sql;
    }

    private function run()
    {
        $r = self::prepare(self::buildSQL(), $this->buildQuery['data'] ?? []);

        $this->buildQuery = [];

        return $r;
    }
}
