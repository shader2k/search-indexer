<?php


namespace Shader2k\SearchIndexer\Drivers;


use Shader2k\SearchIndexer\DataPreparers\ElasticsearchDataPreparer;
use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Exceptions\IndexingException;
use Elasticsearch\ClientBuilder;

class ElasticsearchDriver
{
    const POSTFIX_WRITE = '_write';
    const POSTFIX_READ  = '_read';

    private $dataPreparer;
    private $model;
    private $client;
    private $indexType;
    private $indexName;
    private $indexAliasWrite;
    private $indexAliasRead;
    private $coldIndexName;
    private $hotIndexName;

    public function __construct()
    {
        $this->dataPreparer = new ElasticsearchDataPreparer();
        $this->client = ClientBuilder::create()->build();
    }

    /**
     * Индексирование данных
     * @param array $rawData
     * @param object $model
     * @return bool
     * @throws \ReflectionException
     */
    public function indexingData(array $rawData, object $model): bool
    {
        if (empty($rawData)) {//индексация окончена
            return true;
        }
        $this->setModel($model);
        if (count($rawData) !== count($rawData, COUNT_RECURSIVE)) {
            $data = $this->dataPreparer->toBulk($rawData, $this->getModelParamsToArray());
            return $this->bulk($data);
        } else {
            //todo:Одномерный
            return false;
        }
    }

    /**
     * Подготовка индекса
     * @return bool
     * @throws DriverException
     */
    public function prepareIndex(): bool
    {
        //старый индекс
        $this->coldIndexName = $this->getLatestIndexNameForModel();

        //новый индекс
        $this->hotIndexName = $this->createIndex();
        if ($this->hotIndexName === null) {
            throw new DriverException('Ошибка создания индекса. indexType: ' . $this->indexType . ' (elasticsearch)');
        }

        if ($this->existAlias($this->indexAliasWrite)) {
            //проверка правильность привязки алиаса

            if (false === $this->existAlias($this->indexAliasWrite, $this->hotIndexName)) {
                if (false === $this->deleteAlias($this->indexAliasWrite, $this->findIndexNameByAlias($this->indexAliasWrite))) {
                    throw new DriverException('Ошибка удаления алиаса у индекса . indexType: ' . $this->indexType . ' (elasticsearch)');
                }
            }
        }

        if ($this->addAlias($this->indexAliasWrite, $this->hotIndexName) === false) {
            throw new DriverException('Ошибка создания алиаса у индекса ' . $this->hotIndexName . ' (elasticsearch)');
        }

        return true;
    }

    /**
     * Завершающий шаг индексирования.
     * смена алиаса на чтение и удаление старого индекса
     * @return bool
     * @throws DriverException
     */
    public function deploymentIndex(): bool
    {
        $atomicAliasChangeResult = $this->atomicAliasChange($this->indexAliasRead, $this->hotIndexName, $this->coldIndexName);
        if ($atomicAliasChangeResult === false) {
            return false;
        }

        $deleteIndex = $this->deleteIndex($this->coldIndexName);
        if ($deleteIndex === false) {
            return false;
        }

        return true;
    }

    /**
     * Атомарное удаление алиаса со старого индекса и присвоение новому.
     * @param $aliasName
     * @param $indexName
     * @param $oldIndexName
     * @return bool
     * @throws DriverException
     */
    private function atomicAliasChange($aliasName, $indexName, $oldIndexName): bool
    {
        $params['body']['actions'] = [];
        if ($oldIndexName !== null) {//отвязка алиаса от старого индекса
            $existAlias = $this->client->indices()->existsAlias(['name' => $aliasName, 'index' => $oldIndexName]);
            if ($existAlias) {
                $params['body']['actions'][] = [
                    'remove' => ['index' => $oldIndexName, 'alias' => $aliasName],
                ];
            }
        }

        $params['body']['actions'][] = [
            'add' => ['index' => $indexName, 'alias' => $aliasName],
        ];
        try {
            $response = $this->client->indices()->updateAliases($params);
            if ($response['acknowledged'] === true) {
                return true;
            }
        } catch (\Exception $e) {
            throw new DriverException('Ошибка атомарного обновления алиаса у индекса ' . $this->indexType . ' (elasticsearch)');
        }

        return false;
    }

    /**
     * Удаление индекса
     * @param string $indexName
     * @return bool
     */
    private function deleteIndex(string $indexName): bool
    {
        $params = [
            'index' => $indexName,
        ];
        if ($this->client->indices()->exists($params) === false) {
            return false;
        }

        try {
            $response = $this->client->indices()->delete($params);
            if ($response['acknowledged'] === true) {
                return true;
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . " " . $e->getTrace();
        }

        return false;
    }

    /**
     * Удаление алиаса
     * @param string $aliasName
     * @param string $indexName
     * @return bool
     */
    private function deleteAlias(string $aliasName, string $indexName): bool
    {
        $params = [
            'name' => $aliasName,
            'index' => $indexName
        ];
        $response = $this->client->indices()->deleteAlias($params);
        if ($response['acknowledged'] === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Добавление алиаса
     * @param string $aliasName
     * @param string $indexName
     * @return bool
     */
    private function addAlias(string $aliasName, string $indexName): bool
    {
        $params = [
            'name' => $aliasName,
            'index' => $indexName
        ];
        $response = $this->client->indices()->putAlias($params);
        if ($response['acknowledged'] === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Создание индекса
     * @param bool $postfix
     * @return string|null
     */
    public function createIndex(bool $postfix = false): ?string
    {
        if ($postfix === false) {
            $postfix = '_' . time();
        }
        $params = [
            'index' => $this->indexName . $postfix,
        ];
        if ($this->client->indices()->exists($params) === false) {
            try {
                $response = $this->client->indices()->create($params);

                if ($response['acknowledged'] === true) {
                    return $response['index'];
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . " " . $e->getTrace();
            }
        }

        return null;
    }

    /**
     * Проверка на существование алиаса
     * @param string $aliasName
     * @param string $indexName
     * @return bool
     */
    private function existAlias(string $aliasName, string $indexName = ''): bool
    {
        $params['name'] = $aliasName;
        if ($indexName !== '') {
            $params['index'] = $indexName;
        }
        $aliasExist = $this->client->indices()->existsAlias($params);
        if ($aliasExist === true) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Получить имя самого нового индекса для модели
     * @return string|null
     */
    private function getLatestIndexNameForModel(): ?string
    {
        $namesOfIndexes = $this->filterIndexesByName($this->indexName);
        if (empty($namesOfIndexes)) {
            return null;
        }

        return array_pop($namesOfIndexes);
    }

    /**
     * Фильтрация индексов по имени
     * @param string $indexName
     * @return array
     */
    private function filterIndexesByName(string $indexName): array
    {
        $indexArr = $this->client->cat()->indices(['index' => $indexName . '*']);
        if (empty($indexArr)) {
            return [];
        }
        $indexArr = array_column($indexArr, 'index');
        sort($indexArr);

        return $indexArr;
    }

    /**
     * Поиск имени индекса по алиасу
     * @param string $aliasName
     * @return string
     */
    private function findIndexNameByAlias(string $aliasName): string
    {
        $aliases = $this->client->indices()->getAlias(['name' => $aliasName]);

        foreach ($aliases as $index => $aliasMapping) {
            if (array_key_exists($aliasName, $aliasMapping['aliases'])) {
                return $index;
            }
        }
    }

    /**
     * Массовая вставка
     * @param array $data
     * @return bool
     */
    private function bulk(array $data): bool
    {
        try {
            if (empty($data)) {
                return true;
            }

            $response = $this->client->bulk($data);
            if ($response['errors'] === true) {
                return false;
            }
        } catch (\Exception $e) {
            echo $e->getCode() . ' ' . $e->getMessage();
            return false;
        }
        return true;
    }


    /**
     * Установить модель
     * @param object $model
     * @throws \ReflectionException
     */
    public function setModel(object $model): void
    {
        if (empty($model) === false) {
            $this->model = $model;
            $this->setModelParams();
        }
    }

    /**
     * Получить модель
     * @return array
     */
    public function getModel(): array
    {
        return $this->model;
    }

    /**
     * Установить параметры модели
     * @throws \ReflectionException
     */
    private function setModelParams(): void
    {
        $shortClassName = strtolower((new \ReflectionClass($this->model))->getShortName());
        $className = get_class($this->model);
        $this->indexType = $className;
        $this->indexName = $shortClassName;
        $this->indexAliasWrite = $shortClassName . self::POSTFIX_WRITE;
        $this->indexAliasRead = $shortClassName . self::POSTFIX_READ;

    }

    /**
     * получить параметры модели в виде массива.
     * @return array
     */
    private function getModelParamsToArray(): array
    {
        return [
            'indexType' => $this->indexType,
            'indexName' => $this->indexName,
            'indexAliasWrite' => $this->indexAliasWrite,
            'indexAliasRead' => $this->indexAliasRead,
        ];
    }
}
