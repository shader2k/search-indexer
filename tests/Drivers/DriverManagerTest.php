<?php

namespace Tests\Drivers;

use Shader2k\SearchIndexer\Drivers\DriverManager;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * Class DriverManagerTest
 * @package Tests\Drivers
 */
class DriverManagerTest extends TestCase
{

    public function testInitiateDriver(): void
    {
        $driverManager = new DriverManager();
        $driver = $driverManager->getDriver();
        $this->assertTrue(is_a($driver, ElasticsearchDriver::class, false));

    }

}
