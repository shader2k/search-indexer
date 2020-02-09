<?php

namespace Tests;

use App\User;
use Shader2k\SearchIndexer\Drivers\ElasticsearchDriver;
use Shader2k\SearchIndexer\Traits\HelpersTrait;

class ElasticsearchDriverTest extends TestCase
{
    use HelpersTrait;

    /**
     * Тест индексирования пачки данных
     * @throws \ReflectionException
     */
    public function testPrepareDataBulk()
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
        $response = $elasticsearchDriver->indexingData($rawData, new User());

        $this->assertTrue($response);

    }

    /**
     * Тест подготовки и деплоя индекса
     * @throws \ReflectionException
     * @throws \Shader2k\SearchIndexer\Exceptions\DriverException
     */
    public function testPrepareAndDeploymentIndex()
    {
        $elasticsearchDriver = new ElasticsearchDriver();
        $model = new User();
        $elasticsearchDriver->setModel($model);
        $prepareResponse = $elasticsearchDriver->prepareIndex();
        $deploymentResponse = $elasticsearchDriver->deploymentIndex();
        $this->assertTrue($prepareResponse);
        $this->assertTrue($deploymentResponse);

    }


}
