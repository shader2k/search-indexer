<?php

namespace Tests\Drivers;

use Shader2k\SearchIndexer\Drivers\DriverManager;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Shader2k\SearchIndexer\Traits\HelpersTrait;
use Tests\TestCase;

class DriverTest extends TestCase
{
    use HelpersTrait;

    public function testInitiateDriver(): void
    {
        $driverManager = new DriverManager();
        $driver = $driverManager->getDriver();
        $this->assertTrue(is_a($driver, ElasticsearchDriver::class, false));
        //todo проверка на несуществующий драйвер

    }

}
