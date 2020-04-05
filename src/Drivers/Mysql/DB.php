<?php

namespace Shader2k\SearchIndexer\Drivers\Mysql;

use PDO;
use Shader2k\SearchIndexer\Helpers\Singleton;

class DB extends Singleton
{
    protected $connection = null;

    public function getConnection()
    {
        if ($this->connection !== null) {
            return $this->connection;
        }
        $config = config('indexerconfig.mysqlDriver');

        $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return $this->connection = new PDO($dsn, $config['username'], $config['password'], $opt);
    }
}
