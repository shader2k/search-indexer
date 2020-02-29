<?php


namespace Shader2k\SearchIndexer\Drivers\Elasticsearch;


use Elasticsearch\ClientBuilder;
use Shader2k\SearchIndexer\DataPreparers\ElasticsearchDataPreparer;
use Shader2k\SearchIndexer\Drivers\DriverContract;
use Shader2k\SearchIndexer\Drivers\DriverFactory;

class ElasticsearchDriverFactory extends DriverFactory
{

    /**
     * Получение экземпляра драйвера
     * @return DriverContract
     */
    public function getDriver(): DriverContract
    {
        $dataPreparer = new ElasticsearchDataPreparer();
        //todo проверка на существование env параметра
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();
        return new ElasticsearchDriver($dataPreparer, $client);
    }
}
