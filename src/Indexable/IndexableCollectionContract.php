<?php


namespace Shader2k\SearchIndexer\Indexable;


interface IndexableCollectionContract
{

    /**
     * indexableEntityContract constructor.
     * @param IndaxableContract $item
     */
    public function __construct(IndaxableContract ...$item);

    /**
     * Добавление в коллекцию
     * @param IndaxableContract $item
     * @return $this
     */
    public function push(IndaxableContract $item): void;

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
     * Получение поискового драйвера
     * @return string
     */
    public function getSearchDriver(): ?string;


    /**
     * Получение имени индекса
     * @return string
     */
    public function getIndexName(): ?string;


}
