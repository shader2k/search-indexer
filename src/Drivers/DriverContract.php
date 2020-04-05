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
     * @param bool $reindex необходимо обеспечить бесперебойную работу старого индекса, в случае переиндексации всей модели, если параметр === true
     * @return bool
     */
    public function prepareIndex(string $modelClass, bool $reindex = false): bool;

    /**
     * Завершающий шаг индексирования.
     * смена алиаса на чтение и удаление старого индекса
     * @return bool
     * @throws DriverException
     */
    public function deploymentIndex(): bool;

    /**
     * Удаление индекса
     * @param IndexableCollectionContract $collection
     * @return bool
     */
    public function remove(IndexableCollectionContract $collection): bool;
}
