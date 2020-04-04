<?php

namespace Shader2k\SearchIndexer\Drivers\Elasticsearch;

use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Indexable\IndexableEntityContract;

class ElasticsearchDataPreparer implements DataPreparerContract
{
    /**
     * @param IndexableCollectionContract $collection
     * @param array $modelParams
     * @return array
     */
    public function toBulk(IndexableCollectionContract $collection, array $modelParams): array
    {
        if ($collection->isEmpty()) {
            return [];
        }

        $preparedData = [];
        /** @var IndexableEntityContract $item */
        foreach ($collection as $item) {
            $preparedData['body'][] = [
                'index' => [
                    '_index' => $modelParams['indexAliasWrite'],
                    '_type' => $modelParams['indexType'],
                    '_id' => $item->getIdentifier(),
                ],
            ];
            $preparedData['body'][] = $item->getData();
        }

        return $preparedData;
    }
}
