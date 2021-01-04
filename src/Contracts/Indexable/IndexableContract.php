<?php

namespace Shader2k\SearchIndexer\Contracts\Indexable;

interface IndexableContract
{
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
     * @return string|null
     */
    public static function getSearchDriverName(): ?string;

    /**
     * Получить провайдер
     * @return string|null
     */
    public static function getProviderName(): ?string;

    /**
     * Получить настройки индекса
     * @return array|null
     */
    public static function getIndexParameters(): ?array;

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

}
