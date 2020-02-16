<?php
namespace Shader2k\SearchIndexer;

use Shader2k\SearchIndexer\Drivers\ElasticsearchDriver;
use Shader2k\SearchIndexer\Exceptions\IndexingException;
use Shader2k\SearchIndexer\Providers\EloquentProvider;

class SearchIndexerService
{
    private $chunk;
    private $data;
    private $provider;
    private $driver;
    private $model;

    public function __construct(EloquentProvider $provider, ElasticsearchDriver $driver)
    {
        $this->provider = $provider;
        $this->driver = $driver;
        $this->chunk = 1;
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
            if($this->prepareIndex() === false){
                throw new IndexingException('Ошибка индексации: Ошибка подготовки индекса.');
            }
            while($this->getChunkOfDataFromModel($this->model)){
                $this->indexing();
            }
            if($this->deploymentIndex() === false){
                throw new IndexingException('Ошибка индексации: Ошибка деплоя индекса.');
            }
        }catch (IndexingException $e){
            echo $e->getCode().' '.$e->getMessage();
            return false;
        }

        return true;
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
     */
    protected function indexing(): bool
    {
        return $this->driver->indexingData($this->getData());
    }

    /**
     * Подготовка индекса
     * @return bool
     * @throws Exceptions\DriverException
     * @throws \ReflectionException
     */
    protected function prepareIndex(): bool
    {
        return $this->driver->prepareIndex($this->model);
    }

    /**
     * Завершающий шаг индексирования
     * @return bool
     * @throws Exceptions\DriverException
     */
    protected function deploymentIndex(): bool
    {
        return $this->driver->deploymentIndex();
    }

    public function setData(array $data): void
    {
           $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }




}
