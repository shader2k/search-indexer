<?php

namespace Tests\Drivers;

use App\User;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\CatNamespace;
use Elasticsearch\Namespaces\IndicesNamespace;
use Mockery as m;
use Shader2k\SearchIndexer\DataPreparers\DataPreparerContract;
use Shader2k\SearchIndexer\DataPreparers\ElasticsearchDataPreparer;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Shader2k\SearchIndexer\Indexable\IndexableContract;
use Tests\TestCase;

class ElasticsearchDriverTest extends TestCase
{

    public function testPrepareIndex(): void
    {
        $mockDataPreparer = m::mock(ElasticsearchDataPreparer::class, DataPreparerContract::class);
        $mockClient = m::mock(Client::class);
        $mockUser = m::mock('alias:' . User::class, IndexableContract::class);

        $mockIndices = m::mock(IndicesNamespace::class);
        $mockIndices->shouldReceive('exists')
            ->andReturn(false);
        $mockIndices->shouldReceive('existsAlias')
            ->andReturn(true, false);
        $mockIndices->shouldReceive('deleteAlias')
            ->andReturn(['acknowledged' => true]);
        $mockIndices->shouldReceive('putAlias')
            ->andReturn(['acknowledged' => true]);
        $mockIndices->shouldReceive('create')
            ->andReturn([
                'acknowledged' => true,
                'index' => 'newIndexName'
            ]);
        $mockIndices->shouldReceive('getAlias')
            ->andReturn([
                'indexName' => [
                    'aliases' => ['user_write' => []]
                ]
            ]);

        $mockCat = m::mock(CatNamespace::class);
        $mockCat->shouldReceive('indices')
            ->andReturn([
                ['index' => 'indexName'],
                ['index' => 'indexName2']
            ]);

        $mockClient->shouldReceive('cat')
            ->andReturn($mockCat);
        $mockClient->shouldReceive('indices')
            ->andReturn($mockIndices);

        $driver = new ElasticsearchDriver($mockDataPreparer, $mockClient);
        $response = $driver->prepareIndex(User::class);
        $this->assertTrue($response);
    }

}
