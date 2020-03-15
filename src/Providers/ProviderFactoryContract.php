<?php

namespace Shader2k\SearchIndexer\Providers;

interface ProviderFactoryContract
{
    /**
     * Получение экземпляра провайдера
     * @return ProviderContract
     */
    public function buildProvider(): ProviderContract;
}
