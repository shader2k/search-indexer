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
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableContract;
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
        $this->initializeEnv($appRootPath);
        $this->config = Config::getInstance();
        $this->config->load($appRootPath . '/config/indexerconfig.php');
        $this->providerManager = $providerManager;
        $this->driverManager = $driverManager;
        $this->providerChunkSize = (int)config('indexerconfig.dataProviderChunkSize');
    }

    /**
     * Инициализирует переменные из .env файла в случае, если он существует
     *
     * @access	protected
     * @param	string	$appRootPath
     * @return	void
     */
    protected function initializeEnv(string $appRootPath): void
    {
        if (file_exists($appRootPath . '/.env')) {
            $this->env->load();
        }
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

        try {
            if ($this->prepareIndex(true) === false) {
                throw new IndexingException('Ошибка индексации: Ошибка подготовки индекса.');
            }
            while ($this->getChunkOfDataFromModel($this->model)) {
                $this->indexing();
            }
            if ($this->deploymentIndex() === false) {
                throw new IndexingException('Ошибка индексации: Ошибка деплоя индекса.');
            }
        } catch (IndexingException $e) {
            $this->resetSettings();
            return false;
        }
        $this->resetSettings();
        return true;
    }

    private function setSettings($model): void
    {
        if (is_string($model)) {
            Helper::classExists($model, IndexingException::class);
        }

        Helper::classImplement($model, IndexableContract::class, IndexingException::class);

        /** @var \Shader2k\SearchIndexer\Contracts\Indexable\IndexableContract $model */
        $this->driver = $model::getSearchDriverName();
        $this->provider = $model::getProviderName();
        if (is_object($model)) {
            $this->model = get_class($model);
        } else {
            $this->model = $model;
        }

    }

    /**
     * Подготовка индекса
     * @param bool $reindex
     * @return bool
     * @throws Exceptions\DriverException
     */
    protected function prepareIndex(bool $reindex = false): bool
    {
        return $this->driverManager->getDriver($this->driver)->prepareIndex($this->model, $reindex);
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

    /**
     * Индексирование сущности
     * @param \Shader2k\SearchIndexer\Contracts\Indexable\IndexableContract $entity
     * @return bool
     * @throws Exceptions\DriverException
     * @throws ReflectionException
     */
    public function indexingEntity(IndexableContract $entity): bool
    {
        $this->setSettings($entity);

        try {
            if ($this->prepareIndex() === false) {
                throw new IndexingException('Ошибка индексации: Ошибка подготовки индекса.');
            }
            $this->setData(new IndexableCollection($entity));
            $this->indexing();
        } catch (IndexingException $e) {
            $this->resetSettings();
            return false;
        }
        $this->resetSettings();
        return true;
    }

    /**
     * Удаление сущностей из индекса
     * @param IndexableContract $entity
     * @return bool
     * @throws Exceptions\DriverException
     */
    public function removeEntity(IndexableContract $entity): bool
    {
        $this->setSettings($entity);

        $this->setData(new IndexableCollection($entity));
        if (!$this->remove()) {
            $this->resetSettings();
            return false;
        }

        $this->resetSettings();
        return true;
    }

    /**
     * Удаление сущности из индекса
     * @return bool
     * @throws Exceptions\DriverException
     */
    protected function remove(): bool
    {
        return $this->driverManager->getDriver($this->driver)->remove($this->getData());
    }


}
