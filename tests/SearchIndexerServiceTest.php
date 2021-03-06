<?php

namespace Shader2k\SearchIndexer\Tests;

use App\User;
use Mockery as m;
use ReflectionException;
use Shader2k\SearchIndexer\Contracts\Drivers\DriverContract;
use Shader2k\SearchIndexer\Drivers\DriverManager;
use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Exceptions\ProviderException;
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableContract;
use Shader2k\SearchIndexer\Contracts\Providers\ProviderContract;
use Shader2k\SearchIndexer\Providers\ProviderManager;
use Shader2k\SearchIndexer\SearchIndexerService;
use Shader2k\SearchIndexer\Tests\Data\MockObjects;
use Shader2k\SearchIndexer\Tests\Data\UserModel;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * Class SearchIndexerServiceTest
 * @package Tests
 */
class SearchIndexerServiceTest extends TestCase
{

    /**
     * Тестирование индексирования модели
     * @throws ReflectionException
     * @throws DriverException
     * @throws ProviderException
     */
    public function testIndexingModel(): void
    {
        $mockIC = m::mock(IndexableCollection::class, IndexableCollectionContract::class)->makePartial();
        $mockIC->shouldReceive('isEmpty')
            ->andReturn(false, true);

        $mockUser = m::mock('alias:' . UserModel::class, IndexableContract::class);
        $mockUser->shouldReceive('getProviderName')->times(2)
            ->andReturn('ProviderName');
        $mockUser->shouldReceive('getSearchDriverName')->times(2)
            ->andReturn('DriverName');

        $mockProviderManager = m::mock(ProviderManager::class);
        $mockDriverManager = m::mock(DriverManager::class);
        $mockDriver = m::mock('overload:'.'YourDriver\ElasticsearchDriverClass', DriverContract::class);
        $mockProvider = m::mock('overload:'.'YourProvider\EloquentProviderClass', ProviderContract::class);
        $mockDriverManager->shouldReceive('getDriver')
            ->times(4)
            ->andReturn($mockDriver);
        $mockDriver->shouldReceive('prepareIndex')
            ->times(2)
            ->andReturn(true, false);
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
                $mockIC
            );

        $searchIndexer = new SearchIndexerService($mockProviderManager, $mockDriverManager);
        $index = $searchIndexer->indexingModel(UserModel::class);
        $this->assertTrue($index);

        //тест ошибки подготовки индекса
        $index = $searchIndexer->indexingModel(UserModel::class);
        $this->assertFalse($index);


    }

    /**
     * Тестирование индексации сущности
     * @throws DriverException
     * @throws ReflectionException
     */
    public function testIndexingEntity(): void
    {
        $mockIC = m::mock(IndexableCollection::class, IndexableCollectionContract::class)->makePartial();
        $mockIC->shouldReceive('isEmpty')
            ->andReturn(false, true);

        $mockProviderManager = m::mock(ProviderManager::class);
        $mockDriverManager = m::mock(DriverManager::class);
        $mockDriver = m::mock('overload:'.'YourDriver\ElasticsearchDriverClass', DriverContract::class);
        $mockDriverManager->shouldReceive('getDriver')
            ->times(3)
            ->andReturn($mockDriver);
        $mockDriver->shouldReceive('prepareIndex')
            ->times(2)
            ->andReturn(true, false);
        $mockDriver->shouldReceive('indexingData')
            ->once()
            ->andReturn(true, false);

        $user = MockObjects::getUserObject();
        $searchIndexer = new SearchIndexerService($mockProviderManager, $mockDriverManager);
        $index = $searchIndexer->indexingEntity($user);

        $this->assertTrue($index);

        //тест ошибки подготовки индекса
        $index = $searchIndexer->indexingEntity($user);
        $this->assertFalse($index);

    }

    /**
     * Тестирование удаления сущности
     * @throws DriverException
     * @throws ReflectionException
     */
    public function testRemoveEntity(): void
    {
        $mockProviderManager = m::mock(ProviderManager::class);
        $mockDriverManager = m::mock(DriverManager::class);
        $mockDriver = m::mock('overload:'.'YourDriver\ElasticsearchDriverClass', DriverContract::class);
        $mockDriverManager->shouldReceive('getDriver')
            ->times(2)
            ->andReturn($mockDriver);
        $mockDriver->shouldReceive('remove')
            ->times(2)
            ->andReturn(true, false);

        $user = MockObjects::getUserObject();
        $searchIndexer = new SearchIndexerService($mockProviderManager, $mockDriverManager);
        $index = $searchIndexer->removeEntity($user);

        $this->assertTrue($index);

        //тест ошибки удаления
        $index = $searchIndexer->removeEntity($user);
        $this->assertFalse($index);

    }

    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

}
