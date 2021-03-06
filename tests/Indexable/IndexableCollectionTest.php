<?php

namespace Shader2k\SearchIndexer\Tests\Indexable;

use Mockery as m;
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Indexable\IndexableEntity;
use Shader2k\SearchIndexer\Tests\Data\MockObjects;
use Shader2k\SearchIndexer\Tests\Data\UserModel;
use Shader2k\SearchIndexer\Tests\TestCase;

class IndexableCollectionTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    /**
     * тестирование заполнения коллекции через конструктор
     */
    public function testCollect(): void
    {

        $mockUser = MockObjects::getUserObject(['name' => 'John', 'email' => 'john@example.com']);
        $mockUser2 = MockObjects::getUserObject(['name' => 'Mike', 'email' => 'mike@example.com']);

        $collection = new IndexableCollection($mockUser, $mockUser2);
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

        $mockUser = MockObjects::getUserObject(['name' => 'John', 'email' => 'john@example.com']);

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
        $mockUser = MockObjects::getUserObject(['name' => 'John', 'email' => 'john@example.com']);

        $collection = new IndexableCollection();
        $collection->push($mockUser);
        $indexName = $collection->getIndexName();

        $this->assertStringContainsString(UserModel::class, $indexName);
    }

    /**
     * Тестирование получения драйвера
     */
    public function testGetSearchDriver(): void
    {
        $mockUser = MockObjects::getUserObject(['name' => 'John', 'email' => 'john@example.com']);

        $collection = new IndexableCollection();
        $collection->push($mockUser);
        $driverName = $collection->getSearchDriverName();

        $this->assertStringContainsString('fakeDriver', $driverName);
    }

}
