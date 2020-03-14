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

class IndexableEntityTest extends TestCase
{
    use HelpersTrait;
    use DatabaseMigrations;

    /**
     * тестирование создания индексируемой сущности
     */
    public function testSetIndexData(): void
    {
        $expected = [
            'data' => [
                'name' => 'John',
                'email' => 'john@example.com'
            ],
            'identifier' => '1'
        ];
        factory(User::class)->create(['name' => 'John', 'email' => 'john@example.com']);
        $item = User::query()->first();

        $indexableEntity = new IndexableEntity($item);
        $data = $indexableEntity->getIndexData();

        $this->assertJsonStringEqualsJsonString(
            json_encode($expected),
            json_encode($data)
        );


    }

}
