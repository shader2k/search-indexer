<?php

namespace Shader2k\SearchIndexer\Tests\Drivers;

use Shader2k\SearchIndexer\Contracts\Drivers\DriverContract;
use Shader2k\SearchIndexer\Contracts\Drivers\DriverFactoryContract;
use Shader2k\SearchIndexer\Tests\TestCase;
use Shader2k\SearchIndexer\Drivers\DriverManager;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Mockery as m;

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

        $fakeDriverFactory = m::mock('overload:'.'YourDriver\ElasticsearchDriverFactoryClass', DriverFactoryContract::class);
        $fakeDriver = m::mock('overload:'.'YourDriver\ElasticsearchDriverClass', DriverContract::class);
        $fakeDriverFactory->shouldReceive('buildDriver')
            ->andReturn($fakeDriver);
        $helperMock = m::mock('alias:'.'Shader2k\SearchIndexer\Helpers\Helper');
        $helperMock->shouldReceive('classExists');
        $helperMock->shouldReceive('classImplement');

        $driverManager = new DriverManager();
        $driver = $driverManager->getDriver();
        $this->assertEquals($fakeDriver,$driver);
    }
}
