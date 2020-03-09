<?php

namespace Shader2k\SearchIndexer;

use Dotenv\Dotenv;
use Shader2k\SearchIndexer\Drivers\DriverManager;
use Shader2k\SearchIndexer\Exceptions\IndexingException;
use Shader2k\SearchIndexer\Providers\EloquentProvider;

class SearchIndexerService
{
    public $env;
    private $chunk;
    private $data;
    private $provider;
    private $driverManager;
    private $model;
    private $driver = null;

    public function __construct(EloquentProvider $provider, DriverManager $driverManager)
    {
        $this->env = Dotenv::create(__DIR__);
        $this->env->load();
        $this->provider = $provider;
        $this->driverManager = $driverManager;
        $this->chunk = 1; //todo env param
    }

    /**
     * Индексирование модели
     * @param object $model
     * @return bool
     * @throws Exceptions\DriverException
     * @throws \ReflectionException
     */
    public function indexingModel(object $model): bool
    {
        $this->model = $model;
        try {
            if ($this->prepareIndex() === false) {
                throw new IndexingException('Ошибка индексации: Ошибка подготовки индекса.');
            }
            while ($this->getChunkOfDataFromModel($this->model)) {
                $this->indexing();
            }
            if ($this->deploymentIndex() === false) {
                throw new IndexingException('Ошибка индексации: Ошибка деплоя индекса.');
            }
        } catch (IndexingException $e) {
            echo $e->getCode() . ' ' . $e->getMessage();
            $this->resetSettings();
            return false;
        }
        $this->resetSettings();
        return true;
    }

    /**
     * Подготовка индекса
     * @return bool
     * @throws Exceptions\DriverException
     * @throws \ReflectionException
     */
    protected function prepareIndex(): bool
    {
        return $this->driverManager->getDriver($this->driver)->prepareIndex($this->model);
    }

    /**
     * Получение порции данных из модели
     * @param object $model
     * @return bool
     */
    protected function getChunkOfDataFromModel(object $model): bool
    {
        $chunk = $this->provider->getChunk($model, $this->chunk);
        if (empty($chunk)) {
            return false;
        }
        $this->setData($chunk);
        return true;
    }

    /**
     * Индексирование
     * @return bool
     * @throws \ReflectionException
     * @throws Exceptions\DriverException
     */
    protected function indexing(): bool
    {
        return $this->driverManager->getDriver($this->driver)->indexingData($this->getData());
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Завершающий шаг индексирования
     * @return bool
     * @throws Exceptions\DriverException
     */
    protected function deploymentIndex(): bool
    {
        return $this->driverManager->getDriver($this->driver)->deploymentIndex();
    }

    public function resetSettings(): void
    {
        $this->driver = null;
    }

    public function setDriver($driverName): void
    {
        $this->driver = $driverName;
    }


}
