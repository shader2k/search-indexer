<?php

namespace Shader2k\SearchIndexer\Drivers\Mysql;

use Shader2k\SearchIndexer\DataPreparers\DataPreparerContract;
use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;

class MysqlDataPreparer implements DataPreparerContract
{

    /**
     * @inheritDoc
     */
    public function toBulk(IndexableCollectionContract $collection, array $modelParams): array
    {
        // TODO: Implement toBulk() method.
    }
}
