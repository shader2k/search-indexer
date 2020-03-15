<?php


namespace Shader2k\SearchIndexer\Indexable;


interface IndexableCollectionContract
{

    /**
     * indexableEntityContract constructor.
     * @param IndexableContract $item
     */
    public function __construct(IndexableContract ...$item);

    /**
     * Добавление в коллекцию
     * @param IndexableContract $item
     * @return $this
     */
    public function push(IndexableContract $item): void;

    /**
     * Проверка на пустоту коллеции
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Получить все элементы
     * @return $this
     */
    public function all();


    /**
     * Получение имени индекса
     * @return string
     */
    public function getIndexName(): ?string;


}
