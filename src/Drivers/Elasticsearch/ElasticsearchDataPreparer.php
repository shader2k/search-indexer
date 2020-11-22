<?php

namespace Shader2k\SearchIndexer\Drivers\Elasticsearch;

use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableEntityContract;

class ElasticsearchDataPreparer implements DataPreparerContract
{
    public const VALID_BULK_METHODS = [
        self::BULK_METHOD_INDEX,
        self::BULK_METHOD_DELETE,
        self::BULK_METHOD_CREATE,
        self::BULK_METHOD_UPDATE
    ];
    public const BULK_METHOD_INDEX  = 'index';
    public const BULK_METHOD_DELETE = 'delete';
    public const BULK_METHOD_CREATE = 'create';
    public const BULK_METHOD_UPDATE = 'update';

    /**
     * @param IndexableCollectionContract $collection
     * @param array $modelParams
     * @param string $method
     * @return array
     * @throws DriverException
     */
    public function forBulk(IndexableCollectionContract $collection, array $modelParams, string $method = self::BULK_METHOD_INDEX): array
    {
        if ($collection->isEmpty()) {
            return [];
        }

        if (!in_array($method, self::VALID_BULK_METHODS, true)) {
            throw new DriverException('Недопустимый метод для вставки(forBulk)');
        }

        $preparedData = [];
        /** @var \Shader2k\SearchIndexer\Contracts\Indexable\IndexableEntityContract $item */
        foreach ($collection as $item) {
            $params = [
                '_index' => $modelParams['indexAliasWrite'],
            ];
            if ($item->getIdentifier()) {
                $params['_id'] = $item->getIdentifier();
            }
            $preparedData['body'][] = [
                $method => $params,
            ];
            if ($method !== self::BULK_METHOD_DELETE) {
                $preparedData['body'][] = $item->getData();
            }
        }

        return $preparedData;
    }
}
