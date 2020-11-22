<?php

namespace Shader2k\SearchIndexer\Contracts\Providers;

use Shader2k\SearchIndexer\Contracts\Providers\ProviderContract;

interface ProviderFactoryContract
{
    /**
     * Получение экземпляра провайдера
     * @return ProviderContract
     */
    public function buildProvider(): ProviderContract;
}
