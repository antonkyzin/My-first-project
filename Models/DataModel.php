<?php
declare(strict_types=1);

namespace Models;

use Interfaces\IDataManagement;

/**
 * Class for connect to database and get needed data
 *
 * @package Models
 */
class DataModel
{
    protected \PDO $pdo;

    /**
     * Object for access to session data
     */
    protected IDataManagement $sessionData;

    /**
     * Object for access to file data
     *
     */
    protected IDataManagement $fileData;

    /**
     *
     */
    protected IDataManagement $config;

    /**
     * Set connecting params and connect with database
     *
     * @throws \PDOException
     * @throws \Exception
     */
    public function __construct()
    {
        $this->sessionData = DataRegistry::getInstance()->get('session');
        $this->fileData = DataRegistry::getInstance()->get('file');
        $this->config = DataRegistry::getInstance()->get('config');
        $params = $this->config->getDBdata();
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

    /**
     * Method for insert data to database
     *
     * @param string $tableName
     * @param array $data
     * @return bool|int|mixed
     * @throws \PDOException
     */
    public function insertData(string $tableName, array $data)
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

    /**
     * Method for delete data from database without given condition
     *
     * @param string $tableName
     * @param string $id
     * @return false|int
     */
    public function deleteData(string $tableName, string $id)
    {
        $sql = "DELETE FROM `{$tableName}` WHERE `id` IN ($id)";
        return $this->pdo->exec($sql);
    }

    /**
     * Method for delete data from database with given condition
     *
     * @param string $tableName
     * @param string $whereCondition
     * @return false|int
     */
    public function deleteDataWithWhere(string $tableName, string $whereCondition)
    {
        $sql = "DELETE FROM `{$tableName}` WHERE $whereCondition";
        return $this->pdo->exec($sql);
    }

    /**
     * Select data from database
     *
     * @param string $tableName
     * @param array $field
     * @param string|null $condition
     * @return array|false|int|mixed
     * @throws \PDOException
     */
    public function selectData(string $tableName, array $field, string $condition = null)
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

    /**
     * Select data from database with join tables
     *
     * @param string $fromTable
     * @param array $joinTables
     * @param array $field
     * @param array $joinConditions
     * @param string|null $whereCondition
     * @return array|false|int|mixed
     * @throws \PDOException
     */
    public function selectJoinData(string $fromTable, array $joinTables, array $field, array $joinConditions, string $whereCondition = null)
    {
        $aliasFromTable = $fromTable[0];

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
        $sql .= "FROM `{$fromTable}` {$aliasFromTable}";
        $i = 1;
        $j = 0;
        foreach ($joinTables as $joinTable) {
            $aliasJoinTable = $joinTable[0];
            $joinCondition = $joinConditions[$j];
            foreach ($joinCondition as $key => $value) {
                $sql .= " LEFT JOIN  `$joinTable` $aliasJoinTable" . $i . " ON $key  = $value ";
                $i++;
            }

            $j++;
        }
        if ($whereCondition) {
            $sql .= " WHERE {$whereCondition}";
        }
        $sql = trim($sql);
        try {
            $result = $this->pdo->query($sql);
            return $result->fetchAll();
        } catch (\PDOException $e) {
            return $e->getCode();
        }
    }

    /**
     * Update data in database
     *
     * @param string $tableName
     * @param array $field
     * @param string $condition
     * @return false|int
     */
    public function updateData(string $tableName, array $field, string $condition)
    {
        $sql = "UPDATE `{$tableName}` SET ";
        $i = 1;
        $count = count($field);
        foreach ($field as $key => $value) {
            if ($i == $count) {
                $sql .= "`{$key}`={$this->pdo->quote("$value")} ";
            } else {
                $sql .= "`{$key}`={$this->pdo->quote("$value")}, ";
            }
            $i++;
        }
        $sql .= "WHERE {$condition}";
        return $this->pdo->exec($sql);
    }

    /**
     * Count data in database
     *
     * @param string $fromTable
     * @param string $whereCondition
     * @param string|null $joinTable
     * @param string|null $joinCondition
     * @return array|false
     */
    public function countData(string $fromTable, string $whereCondition, string $joinTable = null, string $joinCondition = null)
    {
        $sql = "SELECT COUNT(*) FROM `$fromTable` $fromTable ";
        if (isset($joinCondition) && isset($joinTable)) {
            $sql .= "LEFT JOIN `$joinTable` $joinTable[0] ON $joinCondition ";
        }
        $sql .= 'WHERE ' . $whereCondition;
        $result = $this->pdo->query($sql);
        return $result->fetchAll();
    }

    /**
     * Get user type
     *
     * @return false|string
     */
    public function isSigned()
    {
        return $this->sessionData->getUser()['type'] ?? false;
    }

    /**
     * Move uploaded file from temporary folder to right folder in project
     *
     * @param string $folder
     * @param null|string $fileName
     * @return string
     */
    public function moveUploadFile(string $folder, string $fileName = null): string
    {
        $fileName = $fileName ?? $folder . '/' . rand() . $this->fileData->getFileName('image');
        move_uploaded_file($this->fileData->getFileTmpName('image'), "Media/images/" . $fileName);
        return $fileName;
    }

    /**
     * Delete file from right folder in project and delete field in database with the file name
     *
     * @param string $table
     * @param string $id
     * @return void
     */
    public function deleteFile(string $table, string $id): void
    {
        $field = ['image'];
        $condition = "`id` IN ($id)";
        $avatars = $this->selectData($table, $field, $condition);
        foreach ($avatars as $avatar) {
            if (isset($avatar['image']) && $avatar['image'] != $table . '/standart_avatar.jpg') {
                unlink("Media/images/" . $avatar['image']);
            }
        }
    }

    /**
     * Check access rights
     *
     * @return false|string
     */
    public function isAccess()
    {
        return $this->sessionData->getUser()['access'] ?? false;
    }
}
