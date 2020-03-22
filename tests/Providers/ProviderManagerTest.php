<?php

namespace Tests\Providers;

use Shader2k\SearchIndexer\Providers\Eloquent\EloquentProvider;
use Shader2k\SearchIndexer\Providers\ProviderManager;
use Tests\TestCase;

class ProviderManagerTest extends TestCase
{

    public function testInitiateProvider(): void
    {
        //todo: после добавления конфига, сделать выбор провайдера в провайдер менеджере
        $driverManager = new ProviderManager();
        $driver = $driverManager->getProvider();
        $this->assertTrue(is_a($driver, EloquentProvider::class, false));

    }

}
