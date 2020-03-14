<?php


namespace Shader2k\SearchIndexer\Indexable;


interface IndaxableContract
{
    /**
     * Получить имя индекса
     * @return string
     */
    public function getIndexName(): string;

    /**
     * Получение идетификатора
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Получение индексируемых полей
     * @return array
     */
    public function getIndexableFields(): array;

    /**
     * Получить поисковый драйвер
     * @return string
     */
    public function getSearchDriver(): ?string;

    /**
     * Получить провайдер
     * @return string
     */
    public function getProvider(): ?string;

}
