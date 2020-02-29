<?php

namespace Shader2k\SearchIndexer\Drivers;

interface DriverFactoryContract
{
    /**
     * Фабричный метод
     * @return DriverContract
     */
    public static function create(): DriverContract;

    /**
     * Получение экземпляра драйвера
     * @return DriverContract
     */
    public function getDriver(): DriverContract;
}
