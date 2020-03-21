<?php

namespace Tests\Drivers;

use Shader2k\SearchIndexer\Drivers\DriverManager;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Tests\TestCase;

class DriverManagerTest extends TestCase
{

    public function testInitiateDriver(): void
    {
        //todo: после добавления конфига, сделать выбор драйвера в драйвер менеджере
        $driverManager = new DriverManager();
        $driver = $driverManager->getDriver();
        $this->assertTrue(is_a($driver, ElasticsearchDriver::class, false));
        //todo проверка на несуществующий драйвер

    }

}
