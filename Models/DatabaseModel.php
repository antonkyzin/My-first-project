<?php

namespace Models;

class DatabaseModel
{
    protected $_db;

    public function __construct()
    {
        $params = require 'data/db_params.php';
        $opt = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC);
        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']}; charset={$params['charset']}";
        try {
            $this->_db = new \PDO($dsn, $params['user'], $params['password'], $opt);
            return $this->_db;
        } catch (\PDOException $e) {
            return $e->getCode();
        }
    }

    public function insertData($tableName, array $data)
    {
        $insert = "INSERT INTO `{$tableName}` (";
        $values = 'VALUES (';
        $i = 1;
        $count = count($data);
        foreach ($data as $field => $value) {
            if ($i == $count) {
                $insert .= "`{$field}`)";
                $values .= ":{$field})";
                break;
            }
            $insert .= "{$field}, ";
            $values .= ":{$field}, ";
            $i++;
        }
        $sql = $insert . $values;
        try {
            $stmt = $this->_db->prepare($sql);
            foreach ($data as $field => $value) {
                $stmt->bindValue(":{$field}", $value);
            }
            return $stmt->execute();
        } catch (\PDOException $e) {
            return $e->getCode();
        }
    }

    public function deleteData($tableName, $id)
    {
        $sql = "DELETE FROM `{$tableName}` WHERE `id` IN ($id)";
        return $this->_db->exec($sql);
    }

    public function selectData($tableName, $field, $condition = null)
    {
        $sql = 'SELECT ';
        $i = 1;
        $count = count($field);
        foreach ($field as $value) {
            if ($i == $count) {
                $sql .= "`{$value}` ";
            } else {
                $sql .= "`{$value}`, ";
            }
        }
        $sql .= "FROM `{$tableName}`";
        if ($condition) {
            $sql .= " WHERE {$condition}";
        }
        try {
            $result = $this->_db->query($sql);
            return $result->fetchAll();
        } catch (\PDOException $e) {
            return $e->getCode();
        }
    }

    public function updateData($tableName, $field, $condition)
    {
        $sql = "UPDATE `{$tableName}` SET ";
        $i = 1;
        $count = count($field);
        foreach ($field as $key => $value) {
            if ($i = $count) {
                $sql .= "`{$key}`={$this->_db->quote($value)} ";
            } else {
                $sql .= "`{$key}`={$this->_db->quote($value)}, ";
            }
        }
        $sql .= "WHERE `{$condition}`";
        return $this->_db->exec($sql);
    }

}