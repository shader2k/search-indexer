<?php

namespace Shader2k\SearchIndexer\Providers\Eloquent;

use Shader2k\SearchIndexer\Contracts\Providers\ProviderContract;
use Shader2k\SearchIndexer\Contracts\Providers\ProviderFactoryContract;

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
