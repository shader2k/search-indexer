<?php

namespace Shader2k\SearchIndexer;

use Composer\Autoload\ClassLoader;
use Dotenv\Dotenv;
use ReflectionClass;
use ReflectionException;
use Shader2k\SearchIndexer\Drivers\DriverManager;
use Shader2k\SearchIndexer\Exceptions\IndexingException;
use Shader2k\SearchIndexer\Exceptions\ProviderException;
use Shader2k\SearchIndexer\Helpers\Config;
use Shader2k\SearchIndexer\Helpers\Helper;
use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Indexable\IndexableContract;
use Shader2k\SearchIndexer\Providers\ProviderManager;

class SearchIndexerService
{
    public $env;
    public $config;
    private $providerChunkSize;
    private $data;
    private $providerManager;
    private $driverManager;
    private $model;
    private $driver = null;
    private $provider = null;

    public function __construct(ProviderManager $providerManager, DriverManager $driverManager)
    {
        $autoloader = new ReflectionClass(ClassLoader::class);
        $appRootPath = dirname($autoloader->getFileName(), 3);
        $this->env = Dotenv::create($appRootPath);
        $this->env->load();
        $this->config = Config::getInstance();
        $this->config->load($appRootPath . '/config/indexerconfig.php');
        $this->providerManager = $providerManager;
        $this->driverManager = $driverManager;
        $this->providerChunkSize = (int)config('indexerconfig.dataProviderChunkSize');
    }

    /**
     * Индексирование модели
     * @param string $model
     * @return bool
     * @throws Exceptions\DriverException
     * @throws ProviderException
     * @throws ReflectionException
     */
    public function indexingModel(string $model): bool
    {
        $this->setSettings($model);

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
     * @throws ReflectionException
     */
    protected function prepareIndex(): bool
    {
        return $this->driverManager->getDriver($this->driver)->prepareIndex($this->model);
    }

    /**
     * Получение порции данных из модели
     * @param string $model
     * @return bool
     * @throws ProviderException
     */
    protected function getChunkOfDataFromModel(string $model): bool
    {
        /** @var IndexableCollectionContract $collection */
        $collection = $this->providerManager->getProvider($this->provider)->getChunk($model, $this->providerChunkSize);
        if ($collection->isEmpty()) {
            return false;
        }
        $this->setData($collection);
        return true;
    }

    /**
     * Индексирование
     * @return bool
     * @throws ReflectionException
     * @throws Exceptions\DriverException
     */
    protected function indexing(): bool
    {
        return $this->driverManager->getDriver($this->driver)->indexingData($this->getData());
    }

    public function getData(): IndexableCollectionContract
    {
        return $this->data;
    }

    public function setData(IndexableCollectionContract $data): void
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
        $this->provider = null;
    }

    private function setSettings(string $modelClass): void
    {
        Helper::classExists($modelClass, IndexingException::class);
        Helper::classImplement($modelClass, IndexableContract::class, IndexingException::class);

        /** @var IndexableContract $modelClass */
        $this->driver = $modelClass::getSearchDriverName();
        $this->provider = $modelClass::getProviderName();
    }


}
