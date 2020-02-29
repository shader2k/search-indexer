<?php

namespace Tests;

use App\User;
use Elasticsearch\ClientBuilder;
use Shader2k\SearchIndexer\DataPreparers\ElasticsearchDataPreparer;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Shader2k\SearchIndexer\Traits\HelpersTrait;

class ElasticsearchDriverTest extends TestCase
{
    use HelpersTrait;

    /**
     * Тест подготовки и деплоя индекса
     * @throws \ReflectionException
     * @throws \Shader2k\SearchIndexer\Exceptions\DriverException
     */
    public function testPrepareAndDeploymentIndex()
    {
        $dataPreparer = new ElasticsearchDataPreparer();
        $client = ClientBuilder::create()->setHosts([getenv('ELASTICSEARCH_HOST')])->build();
        $elasticsearchDriver = new ElasticsearchDriver($dataPreparer, $client);
        $model = new User();
        $prepareResponse = $elasticsearchDriver->prepareIndex($model);
        $deploymentResponse = $elasticsearchDriver->deploymentIndex();
        $this->assertTrue($prepareResponse);
        $this->assertTrue($deploymentResponse);

    }

    /**
     * Тест индексирования пачки данных
     * @throws \ReflectionException
     * @throws \Shader2k\SearchIndexer\Exceptions\DriverException
     */
    public function testIndexingDataBulk()
    {
        $rawData = [
            [
                'id' => 5,
                'name' => 'John',
                'email' => 'test@example.com'

            ],
            [
                'id' => 1,
                'name' => 'Alex',
                'email' => 'example@example.com'

            ]
        ];
        $dataPreparer = new ElasticsearchDataPreparer();
        //todo проверка на существование env параметра
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();
        $elasticsearchDriver = new ElasticsearchDriver($dataPreparer, $client);
        $elasticsearchDriver->prepareIndex(new User());
        $response = $elasticsearchDriver->indexingData($rawData);
        $elasticsearchDriver->deploymentIndex();

        $this->assertTrue($response);

    }


}
