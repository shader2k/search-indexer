<?php

namespace Shader2k\SearchIndexer\DataPreparers;

use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;

interface DataPreparerContract
{
    /**
     * @param IndexableCollectionContract $collection
     * @param array $modelParams
     * @return array
     */
    public function toBulk(IndexableCollectionContract $collection, array $modelParams): array;
}
