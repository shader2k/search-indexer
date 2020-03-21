<?php

namespace Tests\Drivers;

use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Shader2k\SearchIndexer\Indexable\IndexableEntity;
use Shader2k\SearchIndexer\Tests\Data\MockObjects;
use Shader2k\SearchIndexer\Traits\HelpersTrait;
use Tests\TestCase;

class IndexableEntityTest extends TestCase
{
    use HelpersTrait;
    use DatabaseMigrations;

    /**
     * тестирование создания индексируемой сущности
     * @throws Exception
     */
    public function testSetIndexData(): void
    {
        $expected = [
            'name' => 'John',
            'email' => 'john@example.com'
        ];
        $mockUser = MockObjects::getUserObject(['id' => 1, 'name' => 'John', 'email' => 'john@example.com']);
        $indexableEntity = new IndexableEntity($mockUser);

        $this->assertJsonStringEqualsJsonString(
            json_encode($expected),
            json_encode($indexableEntity->getData())
        );
        $this->assertEquals(1, $indexableEntity->getIdentifier());
    }

}
