<?php


namespace Shader2k\SearchIndexer\Indexable;


interface indexableEntityContract
{
    /**
     * indexableEntityContract constructor.
     * @param IndaxableContract $item
     */
    public function __construct(IndaxableContract $item);

    /**
     * Получение данных для индексации сущности
     * @return array
     */
    public function getIndexData(): array;
}
