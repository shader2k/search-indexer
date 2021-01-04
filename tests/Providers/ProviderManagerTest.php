<?php

namespace Shader2k\SearchIndexer\Tests\Providers;

use Shader2k\SearchIndexer\Contracts\Drivers\DriverContract;
use Shader2k\SearchIndexer\Contracts\Drivers\DriverFactoryContract;
use Shader2k\SearchIndexer\Contracts\Providers\ProviderContract;
use Shader2k\SearchIndexer\Contracts\Providers\ProviderFactoryContract;
use Shader2k\SearchIndexer\Providers\Eloquent\EloquentProvider;
use Shader2k\SearchIndexer\Providers\ProviderManager;
use Shader2k\SearchIndexer\Tests\TestCase;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * Class ProviderManagerTest
 * @package Tests\Providers
 */
class ProviderManagerTest extends TestCase
{

    public function testInitiateProvider(): void
    {

        $fakeProviderFactory = m::mock('overload:'.'YourProvider\EloquentProviderFactoryClass', ProviderFactoryContract::class);
        $fakeProvider = m::mock('overload:'.'YourProvider\EloquentProviderClass', ProviderContract::class);
        $fakeProviderFactory->shouldReceive('buildProvider')
            ->andReturn($fakeProvider);
        $helperMock = m::mock('alias:'.'Shader2k\SearchIndexer\Helpers\Helper');
        $helperMock->shouldReceive('classExists');
        $helperMock->shouldReceive('classImplement');
        $providerManager = new ProviderManager();
        $provider = $providerManager->getProvider();
        $this->assertEquals($fakeProvider,$provider);


    }

}
