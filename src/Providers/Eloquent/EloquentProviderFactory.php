<?php


namespace Shader2k\SearchIndexer\Providers\Eloquent;


use Shader2k\SearchIndexer\Providers\ProviderContract;
use Shader2k\SearchIndexer\Providers\ProviderFactoryContract;

class EloquentProviderFactory implements ProviderFactoryContract
{
    /**
     * @inheritDoc
     */
    public function buildProvider(): ProviderContract
    {
        return new EloquentProvider();
    }
}
