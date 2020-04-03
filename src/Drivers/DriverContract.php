<?php

namespace Shader2k\SearchIndexer\Drivers;

use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;

interface DriverContract
{
    /**
     * Индексирование данных
     * @param IndexableCollectionContract $collection
     * @return bool
     */
    public function indexingData(IndexableCollectionContract $collection): bool;

    /**
     * Подготовка индекса
     * @param string $modelClass
     * @return bool
     */
    public function prepareIndex(string $modelClass): bool;

    /**
     * Завершающий шаг индексирования.
     * смена алиаса на чтение и удаление старого индекса
     * @return bool
     * @throws DriverException
     */
    public function deploymentIndex(): bool;
}
