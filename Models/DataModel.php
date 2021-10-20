<?php

namespace Models;

class DatabaseModel
{
    protected $pdo;

    public function __construct()
    {
        $params = require 'data/db_params.php';
        $options = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC);
        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']}; charset={$params['charset']}";
        try {
            $this->pdo = new \PDO($dsn, $params['user'], $params['password'], $options);
            return $this->pdo;
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
            $insert .= "`{$field}`, ";
            $values .= ":{$field}, ";
            $i++;
        }
        $sql = $insert . $values;
        try {
            $stmt = $this->pdo->prepare($sql);
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
        return $this->pdo->exec($sql);
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
            $i++;
        }
        $sql .= "FROM `{$tableName}`";
        if ($condition) {
            $sql .= " WHERE {$condition}";
        }
        try {
            $result = $this->pdo->query($sql);
            return $result->fetchAll();
        } catch (\PDOException $e) {
            return $e->getCode();
        }
    }

    public function selectJoinData($fromTable, $joinTable, $field, array $joinCondition, $whereCondition = null)
    {
        $aliasFromTable = $fromTable[0];
        $aliasJoinTable = $joinTable[0];
        $sql = 'SELECT ';
        $i = 1;
        $count = count($field);
        foreach ($field as $value) {
            if ($i == $count) {
                $sql .= "{$value} ";
            } else {
                $sql .= "{$value}, ";
            }
            $i++;
        }
        $sql .= "FROM {$fromTable} {$aliasFromTable}";
        $i = 1;
        foreach ($joinCondition as $key => $value) {
            if ($i == count($joinCondition) && !$whereCondition) {
                $sql .= " LEFT JOIN  $joinTable $aliasJoinTable" . $i . " ON $key = $value;";
                break;
            }
            $sql .= " LEFT JOIN  $joinTable $aliasJoinTable" . $i . " ON $key  = $value ";
            $i++;
        }
        if ($whereCondition) {
            $sql .= " WHERE {$whereCondition}";
        }
        try {
            $result = $this->pdo->query($sql);
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
            if ($i == $count) {
                $sql .= "`{$key}`={$this->pdo->quote($value)} ";
            } else {
                $sql .= "`{$key}`={$this->pdo->quote($value)}, ";
            }
            $i++;
        }
        $sql .= "WHERE {$condition}";
        return $this->pdo->exec($sql);
    }

    public function isSigned()
    {
        return isset($_SESSION['login']);
    }
}
