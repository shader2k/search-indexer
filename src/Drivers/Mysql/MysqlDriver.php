<?php

namespace Shader2k\SearchIndexer\Drivers\Mysql;

use Shader2k\SearchIndexer\Drivers\AbstractDriver;
use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;

class MysqlDriver extends AbstractDriver
{
    public const WRITE_POSTFIX = '_write';
    private $coldIndexName;
    private $mainTable;
    private $writeTable;
    private $repository;

    public function __construct(MysqlRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function indexingData(IndexableCollectionContract $collection): bool
    {
        // TODO: Implement indexingData() method.
    }

    /**
     * @inheritDoc
     */
    public function prepareIndex(string $modelClass): bool
    {
        $this->init($modelClass);
        //$this->coldIndexName = $this->getColdIndexName();
        $this->createIndex();
        return true;
    }

    protected function init(string $modelClass): void
    {
        parent::setModel($modelClass);
        $this->mainTable = $this->getIndexNameWithPrefix($modelClass);
        $this->writeTable = $this->getIndexNameWithPrefix($modelClass) . self::WRITE_POSTFIX;
    }

    /**
     * Возвращает имя индексной таблицы с префиксом
     * @param string $modelClass
     * @return string
     */
    private function getIndexNameWithPrefix(string $modelClass): string
    {
        return mb_strtolower(
            config('indexerconfig.mysqlDriver.prefix') . str_replace(['\\', '.'], "_", $modelClass)
        );
    }

    private function createIndex(): bool
    {
        if ($this->repository->existTable($this->writeTable)) {
            throw new DriverException('Ошибка создания индексной таблицы. Таблица уже существует');
        }
        if ($this->repository->createTable($this->modelClass, $this->writeTable) === false) {
            throw new DriverException('Ошибка создания новой индексной таблицы');
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deploymentIndex(): bool
    {
        // TODO: Implement deploymentIndex() method.
    }

    /**
     * получение имени старого индекса
     * @return string
     */
    private function getColdIndexName(): ?string
    {
        if ($this->repository->existTable($this->mainTable) === false) {
            return null;
        }
        return $this->mainTable;
    }
}
