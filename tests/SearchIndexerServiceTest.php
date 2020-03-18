<?php

namespace Shader2k\SearchIndexer\Tests;

use App\User;
use Mockery as m;
use Shader2k\SearchIndexer\Drivers\DriverContract;
use Shader2k\SearchIndexer\Drivers\DriverManager;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Indexable\IndexableContract;
use Shader2k\SearchIndexer\Providers\Eloquent\EloquentProvider;
use Shader2k\SearchIndexer\Providers\ProviderContract;
use Shader2k\SearchIndexer\Providers\ProviderManager;
use Shader2k\SearchIndexer\SearchIndexerService;
use Tests\TestCase;


class SearchIndexerServiceTest extends TestCase
{

    public function testIndexingModel(): void
    {
        $mockIC = m::mock(IndexableCollection::class, IndexableCollectionContract::class)->makePartial();
        $mockIC->shouldReceive('isEmpty')
            ->andReturn(false, true);

        $mockUser = m::mock('alias:' . User::class, IndexableContract::class);
        $mockUser->shouldReceive('getProviderName')->once()
            ->andReturn('ProviderName');
        $mockUser->shouldReceive('getSearchDriverName')->once()
            ->andReturn('DriverName');

        $mockProviderManager = m::mock(ProviderManager::class);
        $mockDriverManager = m::mock(DriverManager::class);
        $mockDriver = m::mock('alias:' . ElasticsearchDriver::class, DriverContract::class);
        $mockProvider = m::mock('alias:' . EloquentProvider::class, ProviderContract::class);
        $mockDriverManager->shouldReceive('getDriver')
            ->times(3)
            ->andReturn($mockDriver);
        $mockDriver->shouldReceive('prepareIndex')
            ->once()
            ->andReturn(true);
        $mockDriver->shouldReceive('indexingData')
            ->once()
            ->andReturn(true, false);
        $mockDriver->shouldReceive('deploymentIndex')
            ->once()
            ->andReturn(true);

        $mockProviderManager->shouldReceive('getProvider')
            ->times(2)
            ->andReturn($mockProvider);

        $mockProvider->shouldReceive('getChunk')
            ->times(2)
            ->andReturn(
                $mockIC,
                $mockIC
            );

        $searchIndexer = new SearchIndexerService($mockProviderManager, $mockDriverManager);
        $index = $searchIndexer->indexingModel(User::class);

        $this->assertTrue($index);


    }

    protected function tearDown(): void
    {
        m::close();
    }

}
