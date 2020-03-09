<?php

namespace Shader2k\SearchIndexer\Drivers;

interface DriverFactoryContract
{
    /**
     * Получение экземпляра драйвера
     * @return DriverContract
     */
    public function buildDriver(): DriverContract;
}
