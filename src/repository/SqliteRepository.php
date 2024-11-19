<?php

namespace repository;

use core\logger\Logger;
use php\lib\str;
use php\sql\SqlConnection;
use php\sql\SqlDriverManager;
use php\sql\SqlStatement;
use php\sql\SqlException;

abstract class SqliteRepository extends AbstractRepository
{
    /**
     * @var SqliteRepository
     */
    private static $instance;

    /**
     * @var SqlConnection
     */
    protected static $connection;

    public abstract function makeTable ();

    /**
     * DBConnection constructor.
     * @param $file
     */
    public function __construct($file)
    {
        try {
            SqlDriverManager::install('sqlite');
            self::$connection = SqlDriverManager::getConnection('sqlite:' . $file, []);
            $this->makeTable();
        } catch (SqlException $e) {
            Logger::error("Error create connection: " . $e->getMessage());
        }
    }

    /**
     * @param $sql
     * @param array $param
     * @return SqlStatement
     */
    public function query($sql, array $param = []): SqlStatement
    {
        Logger::info("Execute query: " . $sql);

        return self::$connection->query($sql, $param);
    }

    /**
     * @param $name
     * @param array $fields
     * @throws SqlException
     */
    public function createTable($name, array $fields)
    {
        $params = [];

        foreach ($fields as $filedName => $filedParam) {
            /*if ($filedParam instanceof SQLField) {
                $params[] = $filedParam->generate();
                continue;
            }*/

            $params[] = sprintf('`%s` %s', $filedName, $filedParam);
        }

        $sql = sprintf("create table if not exists `%s` (%s)", $name, str::join($params, ', '));
        $this->query($sql)->update();
    }
}