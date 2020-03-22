<?php


namespace Shader2k\SearchIndexer\Drivers\Elasticsearch;


use Elasticsearch\Client;
use Exception;
use ReflectionException;
use Shader2k\SearchIndexer\DataPreparers\ElasticsearchDataPreparer;
use Shader2k\SearchIndexer\Drivers\DriverContract;
use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Helpers\Helper;
use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Indexable\IndexableContract;

class ElasticsearchDriver implements DriverContract
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

    public function __construct(ElasticsearchDataPreparer $dataPreparer, Client $client)
    {
        $this->dataPreparer = $dataPreparer;
        $this->client = $client;
    }

    /**
     * Индексирование данных
     * @param IndexableCollectionContract $collection
     * @return bool
     */
    public function indexingData(IndexableCollectionContract $collection): bool
    {
        if ($collection->isEmpty()) {//индексация окончена
            return false;
        }
        $data = $this->dataPreparer->toBulk($collection, $this->getModelParamsToArray());
        return $this->bulk($data);

    }

    /**
     * Подготовка индекса
     * @param string $model
     * @return bool
     * @throws DriverException
     * @throws ReflectionException
     */
    public function prepareIndex(string $model): bool
    {
        $this->setModel($model);
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

        if ($this->coldIndexName !== null) {
            $deleteIndex = $this->deleteIndex($this->coldIndexName);
            if ($deleteIndex === false) {
                return false;
            }
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        return $response['acknowledged'] === true;
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
        return $response['acknowledged'] === true;
    }

    /**
     * Создание индекса
     * @param string $postfix
     * @return string|null
     */
    private function createIndex(string $postfix = ''): ?string
    {
        if ($postfix === '') {
            $postfix = '_' . microtime(true);
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
            } catch (Exception $e) {
                echo $e->getMessage() . ' ' . $e->getTrace();
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
        return $aliasExist === true;
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
     * @throws DriverException
     */
    private function findIndexNameByAlias(string $aliasName): string
    {
        $aliases = $this->client->indices()->getAlias(['name' => $aliasName]);

        foreach ($aliases as $index => $aliasMapping) {
            if (array_key_exists($aliasName, $aliasMapping['aliases'])) {
                return $index;
            }
        }
        throw new DriverException('Не найден алиас: ' . $aliasName);
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
                throw new DriverException('Ошибка вставки данных в индекс.');
            }
        } catch (Exception $e) {
            echo $e->getCode() . ' ' . $e->getMessage();
            return false;
        }
        return true;
    }


    /**
     * Установить модель
     * @param string $modelClass
     * @throws ReflectionException
     */
    public function setModel(string $modelClass): void
    {
        Helper::classExists($modelClass, DriverException::class);
        Helper::classImplement($modelClass, IndexableContract::class, DriverException::class);
        if (empty($modelClass) === false) {
            $this->model = $modelClass;
            $this->setModelParams();

        }
    }

    /**
     * Получить модель
     * @return object
     */
    public function getModel(): object
    {
        return $this->model;
    }

    /**
     * Установить параметры модели
     * @throws ReflectionException
     */
    private function setModelParams(): void
    {
        $shortClassName = strtolower(substr(strrchr($this->model, "\\"), 1));
        $this->indexType = $this->model;
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
