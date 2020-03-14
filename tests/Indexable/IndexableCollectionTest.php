<?php

namespace Tests\Drivers;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Shader2k\SearchIndexer\Drivers\DriverManager;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Indexable\IndexableEntity;
use Shader2k\SearchIndexer\Traits\HelpersTrait;
use Tests\TestCase;

class IndexableCollectionTest extends TestCase
{
    use HelpersTrait;
    use DatabaseMigrations;

    /**
     * тестирование заполнения коллекции через конструктор
     */
    public function testCollect(): void
    {
        factory(User::class)->create(['name' => 'John', 'email' => 'john@example.com']);
        factory(User::class)->create();

        $items = User::query()->paginate(2, ['*'], 'page', 1);
        $collection = new IndexableCollection(...$items->all());
        $collectionItems = $collection->all();

        $this->assertNotEmpty($collectionItems);
        $this->assertCount(2, $collectionItems);
        $this->assertContainsOnlyInstancesOf(IndexableEntity::class, $collectionItems);
    }

    /**
     * тестирование Push метода
     */
    public function testPush(): void
    {
        $mockUser = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockUser->method('getIndexName')
            ->willReturn('fakeIndexName');

        $mockUser->id = 1;
        $mockUser->name = 'John';
        $mockUser->email = 'john@example.com';

        $collection = new IndexableCollection();
        $collection->push($mockUser);
        $collectionItems = $collection->all();

        $this->assertNotEmpty($collectionItems);
        $this->assertCount(1, $collectionItems);
        $this->assertContainsOnlyInstancesOf(IndexableEntity::class, $collectionItems);
    }

    /**
     * Тестирование получения имени индекса
     */
    public function testGetIndexName(): void
    {
        $mockUser = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIndexName'])
            ->getMock();
        $mockUser->method('getIndexName')
            ->willReturn('fakeIndexName');

        $mockUser->id = 1;
        $mockUser->name = 'John';
        $mockUser->email = 'john@example.com';

        $collection = new IndexableCollection();
        $collection->push($mockUser);
        $indexName = $collection->getIndexName();

        $this->assertStringContainsString('fakeIndexName', $indexName);
    }

    /**
     * Тестирование получения драйвера
     */
    public function testGetSearchDriver(): void
    {

        $mockUser = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSearchDriver'])
            ->getMock();
        $mockUser->method('getSearchDriver')
            ->willReturn('fakeDriver');

        $mockUser->id = 1;
        $mockUser->name = 'John';
        $mockUser->email = 'john@example.com';

        $collection = new IndexableCollection();
        $collection->push($mockUser);
        $driverName = $collection->getSearchDriver();

        $this->assertStringContainsString('fakeDriver', $driverName);
    }

}
