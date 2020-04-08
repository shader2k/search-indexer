<?php

namespace Tests\Providers;

use Shader2k\SearchIndexer\Providers\Eloquent\EloquentProvider;
use Shader2k\SearchIndexer\Providers\ProviderManager;
use Tests\TestCase;

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
        $providerManager = new ProviderManager();
        $provider = $providerManager->getProvider();
        $this->assertTrue(is_a($provider, EloquentProvider::class, false));

    }

}
