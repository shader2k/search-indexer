<?php

namespace Tests;

use App\User;
use Shader2k\SearchIndexer\Drivers\ElasticsearchDriver;
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
        $elasticsearchDriver = new ElasticsearchDriver();
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
        $elasticsearchDriver = new ElasticsearchDriver();
        $elasticsearchDriver->prepareIndex(new User());
        $response = $elasticsearchDriver->indexingData($rawData);
        $elasticsearchDriver->deploymentIndex();

        $this->assertTrue($response);

    }




}
