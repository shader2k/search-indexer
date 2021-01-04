<?php

namespace Shader2k\SearchIndexer\Contracts\Indexable;

use Shader2k\SearchIndexer\Contracts\Indexable\IndexableContract;

interface IndexableEntityContract
{
    /**
     * indexableEntityContract constructor.
     * @param IndexableContract $item
     */
    public function __construct(IndexableContract $item);

    /**
     * Получение данных для индексации сущности
     * @return array
     */
    public function getData(): array;

    /**
     * Получение идентификатора сущности
     * @return string
     */
    public function getIdentifier(): string;
}
