<?php


namespace Shader2k\SearchIndexer\Drivers;


use Shader2k\SearchIndexer\Exceptions\IndexingException;

class ElasticsearchDriver
{
    const POSTFIX_WRITE = '_write';
    const POSTFIX_READ = '_read';
    protected $model;

    public function __construct()
    {
    }

    public function indexingData(array $data, string $model): bool
    {
        if (count($data) !== count($data, COUNT_RECURSIVE)) {
            $this->setModel($model);
            $this->bulk($data);
        } else {
            //Одномерный
        }
    }


    private function bulk($rawData): bool
    {
        try {
            if (true === $models->isEmpty()) {
                return false;
            }

            $params = ['body' => []];
            $attribiteFiltering = new AttributeFiltering();
            $filterName = 'filter'.(new \ReflectionClass($models->first()))->getShortName();
            $typeIndex = get_class($models->first());
            $indexNameWithPostfix = $indexName.self::POSTFIX_WRITE;
            foreach ($models as $model) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $indexNameWithPostfix,
                        '_type' => $typeIndex,
                        '_id' => $model->id,
                    ],
                ];
                $params['body'][] = $attribiteFiltering->$filterName($model);
            }
            $this->client->bulk($params);
        } catch (IndexingException $e) {
            echo $e->getCode().' '.$e->getMessage();
            return false;
        }
        return true;
    }
    private function prepareDataBulk($rawData): array
    {
        if(empty($rawData)){
            return [];
        }
        foreach ($rawData as $row) {
            $preparedData['body'][] = [
                'index' => [
                    '_index' => $this->model.self::POSTFIX_WRITE,
                    '_type' => $this->model,
                    '_id' => $row['id'],
                ],
            ];
            unset($row['id']);
            $preparedData['body'][] = $row;
        }
        return $preparedData;
    }

    public function setModel(string $modelName): void
    {
        if (empty($modelName) === false) {
            $this->model = $modelName;
        }
    }

    public function getModel(): array
    {
        return $this->model;
    }
}
