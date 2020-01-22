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

    public function indexingModel(string $modelName): bool
    {
        $this->model = $modelName;
        try {

            while($this->getChunkOfDataFromModel($modelName)){
                $this->indexing();
            }
        }catch (IndexingException $e){
            echo $e->getCode().' '.$e->getMessage();
            return false;
        }

        return true;
    }

    protected function getChunkOfDataFromModel($modelName): bool
    {
        $chunk = $this->provider->getChunk($modelName, $this->chunk);
        if (empty($chunk['data'])) {
            return false;
        }
        $this->setData($chunk['data']);
        return true;
    }

    private function indexing(): bool
    {
        return $this->driver->indexingData($this->data);
    }

    public function setData(array $data): void
    {
        if (empty($data) === false) {
           $this->data = $data;
        }
    }

    public function getData(): array
    {
        return $this->data;
    }



}
