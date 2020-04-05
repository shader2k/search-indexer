<?php

namespace Shader2k\SearchIndexer\Drivers\Elasticsearch;

use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;

interface DataPreparerContract
{
    /**
     * @param IndexableCollectionContract $collection
     * @param array $modelParams
     * @param string $method
     * @return array
     */
    public function forBulk(IndexableCollectionContract $collection, array $modelParams, string $method = 'index'): array;
}
