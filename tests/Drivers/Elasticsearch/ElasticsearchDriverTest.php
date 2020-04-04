<?php

namespace Tests\Drivers;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\CatNamespace;
use Elasticsearch\Namespaces\IndicesNamespace;
use Mockery as m;
use ReflectionException;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\DataPreparerContract;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDataPreparer;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Tests\Data\MockObjects;
use Shader2k\SearchIndexer\Tests\Data\UserModel;
use Tests\TestCase;

class ElasticsearchDriverTest extends TestCase
{

    /**
     * Переиндексация модели
     * @throws ReflectionException
     * @throws DriverException
     */
    public function testRendexModel(): void
    {
        $preparedData = [
            'body' => [
                [
                    'index' => [
                        '_index' => 'usermodel_write',
                        '_type' => 'App/User',
                        '_id' => 5
                    ]
                ],
                [
                    'name' => 'John',
                    'email' => 'test@example.com'
                ]
            ]

        ];
        $mockDataPreparer = m::mock(ElasticsearchDataPreparer::class, DataPreparerContract::class);
        $mockDataPreparer->shouldReceive('toBulk')
            ->once()
            ->andReturn($preparedData);
        $mockClient = m::mock(Client::class);
        $mockClient->shouldReceive('bulk')
            ->once()
            ->andReturn(['errors' => false]);

        $mockIndices = m::mock(IndicesNamespace::class);
        $mockIndices->shouldReceive('exists')
            ->times(2)
            ->andReturn(false, true);
        $mockIndices->shouldReceive('existsAlias')
            ->times(3)
            ->andReturn(true, false, true);
        $mockIndices->shouldReceive('deleteAlias')
            ->once()
            ->andReturn(['acknowledged' => true]);
        $mockIndices->shouldReceive('putAlias')
            ->once()
            ->andReturn(['acknowledged' => true]);
        $mockIndices->shouldReceive('create')
            ->once()
            ->andReturn([
                'acknowledged' => true,
                'index' => 'newIndexName'
            ]);
        $mockIndices->shouldReceive('getAlias')
            ->once()
            ->andReturn([
                'indexName' => [
                    'aliases' => ['usermodel_write' => []]
                ]
            ]);
        $mockIndices->shouldReceive('updateAliases')
            ->once()
            ->andReturn(['acknowledged' => true]);
        $mockIndices->shouldReceive('delete')
            ->once()
            ->andReturn(['acknowledged' => true]);

        $mockCat = m::mock(CatNamespace::class);
        $mockCat->shouldReceive('indices')
            ->once()
            ->andReturn([
                ['index' => 'indexName'],
                ['index' => 'indexName2']
            ]);

        $mockClient->shouldReceive('cat')
            ->andReturn($mockCat);
        $mockClient->shouldReceive('indices')
            ->andReturn($mockIndices);

        $driver = new ElasticsearchDriver($mockDataPreparer, $mockClient);
        $response = $driver->prepareIndex(UserModel::class);
        $this->assertTrue($response);

        //тестирование индексации
        $collection = MockObjects::getIndexableCollection(2);
        $response = $driver->indexingData($collection);
        $this->assertTrue($response);
        $collection = MockObjects::getIndexableCollection(0);
        //пустая коллекция
        $response = $driver->indexingData($collection);
        $this->assertFalse($response);

        //тестирование деплоя нового индекса
        $response = $driver->deploymentIndex();
        $this->assertTrue($response);
    }

}
