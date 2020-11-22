<?php

namespace Shader2k\SearchIndexer\Contracts\Drivers;

use Shader2k\SearchIndexer\Contracts\Drivers\DriverContract;

interface DriverFactoryContract
{
    /**
     * Получение экземпляра драйвера
     * @return DriverContract
     */
    public function buildDriver(): DriverContract;
}
