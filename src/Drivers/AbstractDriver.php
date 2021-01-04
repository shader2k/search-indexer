<?php

namespace Shader2k\SearchIndexer\Drivers;

use Shader2k\SearchIndexer\Contracts\Drivers\DriverContract;
use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Helpers\Helper;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableContract;

abstract class AbstractDriver implements DriverContract
{
    protected $modelClass;

    /**
     * @inheritDoc
     * @throws DriverException
     */
    protected function setModel(string $modelClass): void
    {
        if (empty($modelClass)) {
            throw new DriverException('Не передан класс модели');
        }
        Helper::classExists($modelClass, DriverException::class);
        Helper::classImplement($modelClass, IndexableContract::class, DriverException::class);

        $this->modelClass = $modelClass;
    }

}
