<?php

namespace Shader2k\SearchIndexer\Drivers\Mysql\QueryBuilder;

class Create
{
    private $query;

    public function create($params): string
    {
        $body = ['`id` int(10) unsigned NOT NULL auto_increment'];
        $keys = [' PRIMARY KEY (`id`)'];
        $i = 0;
        $this->query = 'CREATE TABLE `:table` (';
        foreach ($params as $value) {
            $i++;
            $body[] = "`:{$value}` text default NULL";
            $keys[] = "FULLTEXT KEY `ft{$i}` (`:{$value}`)";
        }

        $this->query .= implode(',', $body);
        $this->query .= ',';
        $this->query .= implode(',', $keys);
        $this->query .= ')';
        return $this->query;

    }
}
