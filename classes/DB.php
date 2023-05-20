<?php

namespace classes;

use PDO;
use classes\Message;
use PDOStatement;

/*
 *  INSERT INTO `table` (columns) VALUES (values);
 *  DELETE FROM `table` WHERE conditions;
 */

class DB
{
    public static PDO $connection;

    public static function connect() {
        if(!isset(self::$connection)) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $dsn = "mysql:host=".db_host.";dbname=".db_name.";port=3306;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$connection = new PDO($dsn, db_user, db_pass, $options);
            }
            catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
    }

    /**
     * Inserts new row and returns inserted row ID
     *
     * @param string $table
     * @param array|null $data
     * @return string|void
     */
    public static function insert(string $table, array $data = null) {
        if($data === null) return;
        if(count($data) == 0) return;

        self::connect();

        $columns = '';
        $values = '';
        foreach($data as $column => $value) {
            $columns .= $column.',';
            $values .= ':'.$column.',';
        }
        $columns = rtrim($columns, ',');
        $values = rtrim($values, ',');

        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
        self::$connection->prepare($query)->execute($data);

        $lastId = self::$connection->lastInsertId();

        return self::getOneByID($table, $lastId);
    }


    /**
     * Get one row by primary key and return array or null if not found
     *
     * @param string $table
     * @param int $id
     * @param string $primary_key
     * @return mixed|null
     */
    public static function getOneByID(string $table, int $id, string $primary_key = '') {
        if($primary_key == '') {
            $primary_key = $table.'_id';
        }

        self::connect();

        $query = "SELECT * FROM {$table} WHERE {$primary_key}={$id} LIMIT 1";
        $stmt = self::$connection->query($query);
        if($stmt->rowCount() != 0)
            return $stmt->fetch();

        return null;
    }


    /**
     * Prepare WHERE conditions from array
     *
     * @param array $where
     * @return array
     */
    private static function where(array $where): array
    {

        $condition = '';
        $attributes = array();

        if(count($where) == 0) {
            $condition = ':condition';
            $attributes['condition'] = 1;
        }
        else {
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $c = count($value);
                    if ($c == 2) {
                        $condition .= $value[0] . ' = :' . $value[0];
                        $attributes[$value[0]] = $value[1];
                    } else if ($c == 3) {
                        $condition .= $value[0] . ' ' . $value[1] . ' :' . $value[0];
                        $attributes[$value[0]] = $value[2];
                    }
                }
                else {
                    $condition .= $key.' = :'.$key;
                    $attributes[$key] = $value;
                }
            }
        }

        return [
            'condition' => $condition,
            'attributes' => $attributes
        ];
    }


    /**
     * Get one row using WHERE
     *
     * @param string $table
     * @param array|null $where
     * @return mixed
     */
    public static function getOne(string $table, array $where = null) {
        self::connect();

        $whereParsed = self::where($where);
        $condition = $whereParsed['condition'];

        $query = "SELECT * FROM {$table} WHERE {$condition} LIMIT 1";

        $stmt = self::$connection->prepare($query);
        $stmt->execute($whereParsed['attributes']);
        return $stmt->fetch();
    }

    public static function getMany($table, $where, $limit = [0, 1]) {
        self::connect();
    }


    /**
     * Update row, return true if successful or false if not
     *
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool
     */
    public static function update(string $table, array $data, array $where): bool
    {
        self::connect();

        $whereParsed = self::where($where);
        $condition = $whereParsed['condition'];

        $sets = '';
        foreach($data as $column => $value) {
            $sets .= $column.'=:'.$column.',';
        }
        $sets = rtrim($sets, ',');

        // where attributes must be added to $data in order to 'prepare' work as intended
        $data = array_merge($data, $whereParsed['attributes']);

        $query = "UPDATE {$table} SET {$sets} WHERE {$condition} LIMIT 1";

        $stmt = self::$connection->prepare($query);
        $stmt->execute($data);

        if($stmt->rowCount())
            return true;

        return false;
    }


    /**
     * Delete row by its ID and return true if successful
     *
     * @param string $table
     * @param int $id
     * @param string $primary_key
     * @return bool
     */
    public static function deleteById(string $table, int $id, string $primary_key = ''): bool
    {
        if($primary_key == '') {
            $primary_key = $table.'_id';
        }

        self::connect();

        $query = "DELETE FROM $table WHERE $primary_key=$id LIMIT 1";
        $stmt = self::$connection->query($query);
        if($stmt->rowCount() != 0)
            return true;

        return false;
    }


    /**
     * Delete multiple rows
     *
     * @param string $table
     * @param array $where
     * @return bool
     */
    public static function delete(string $table, array $where): bool
    {
        self::connect();

        $whereParsed = self::where($where);
        $condition = $whereParsed['condition'];

        $query = "DELETE FROM {$table} WHERE {$condition}";

        $stmt = self::$connection->prepare($query);
        $stmt->execute($whereParsed['attributes']);

        if($stmt->rowCount())
            return true;

        return false;
    }

    /**
     * Raw query
     * @param string $query
     * @return false|PDOStatement
     */
    public static function query(string $query) {
        self::connect();
        return self::$connection->query($query);
    }
}