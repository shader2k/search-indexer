<?php

namespace Tests\Drivers;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
            'name' => 'John',
            'email' => 'john@example.com'
        ];
        $user = factory(User::class)->make(['id' => '1', 'name' => 'John', 'email' => 'john@example.com']);
        $indexableEntity = new IndexableEntity($user);

        $this->assertJsonStringEqualsJsonString(
            json_encode($expected),
            json_encode($indexableEntity->getData())
        );
        $this->assertEquals(1, $indexableEntity->getIdentifier());
    }

}
