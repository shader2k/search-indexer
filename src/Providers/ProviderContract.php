<?php

namespace Shader2k\SearchIndexer\Providers;

use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;

interface ProviderContract
{
    /**
     * @param string $model
     * @param int $chunkSize
     * @return array
     */
    public function getChunk(string $model, int $chunkSize): IndexableCollectionContract;

    /**
     * Установка следующей страницы пагинации
     * @param int $page
     */
    public function setNextPage(int $page): void;

    /**
     * Получение следующей страницы пагинации
     * @return int
     */
    public function getNextPage(): int;
}
