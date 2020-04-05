<?php


namespace Shader2k\SearchIndexer\Drivers\Mysql;


use PDO;
use Shader2k\SearchIndexer\Drivers\Mysql\QueryBuilder\Create;

class MysqlRepository
{
    /**
     * @var PDO $db
     */
    protected $db;

    public function __construct()
    {
        $this->db = DB::getInstance()->getConnection();
    }

    public function getTableNameToRead(string $className): ?string
    {
        $user = $this->db->query('SELECT name FROM users');
        while ($row = $user->fetch()) {
            echo $row['name'] . "\n";
        }
        die();
        return '';
    }

    public function existTable(string $tableName): bool
    {
        $table = $this->db->query("SHOW TABLES LIKE '{$tableName}'");
        return (bool)$table->rowCount();
    }

    public function createTable(string $modelClass, string $writeTable): bool
    {

//        $table = $this->db->query("SHOW TABLES");
//        while ($row = $table->fetch())
//        {
//            var_dump($row);
//        }
//        die();
        $create = new Create();
        $query = $create->create($modelClass::getIndexableFields());

        /*
         *  CREATE TABLE `articles` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `title` varchar(200) default NULL,
                `body` text,
                PRIMARY KEY (`id`),
                FULLTEXT KEY `ft1` (`title`,`body`),
                FULLTEXT KEY `ft2` (`body`)
            )'
         */

        $stmt = $this->db->prepare($query);

        $stmt->execute(['email' => 'email3', 'name' => 'name3', 'table' => 'table3']);
        return true;

    }

}
