<?php

namespace Shader2k\SearchIndexer\Drivers\Mysql;

use Shader2k\SearchIndexer\Drivers\DriverContract;
use Shader2k\SearchIndexer\Drivers\DriverFactoryContract;

class MysqlDriverFactory implements DriverFactoryContract
{

    /**
     * @inheritDoc
     */
    public function buildDriver(): DriverContract
    {
        return new MysqlDriver(new MysqlRepository());
    }
}
