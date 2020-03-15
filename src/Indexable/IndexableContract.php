<?php


namespace Shader2k\SearchIndexer\Indexable;


interface IndexableContract
{
    /**
     * Получить имя индекса
     * @return string
     */
    public function getIndexName(): string;

    /**
     * Получение значения идетификатора
     * @return string
     */
    public function getIdentifierValue(): string;

    /**
     * Получение названия поля идетификатора
     * @return string
     */
    public static function getIdentifierField(): string;

    /**
     * Получение индексируемых полей
     * @return array
     */
    public static function getIndexableFields(): array;

    /**
     * Получить поисковый драйвер
     * @return string
     */
    public static function getSearchDriverName(): ?string;

    /**
     * Получить провайдер
     * @return string
     */
    public static function getProviderName(): ?string;

}
