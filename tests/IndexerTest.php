<?php

namespace Tests;

use App\User;
use Shader2k\SearchIndexer\Drivers\ElasticsearchDriver;
use Shader2k\SearchIndexer\Providers\EloquentProvider;
use Shader2k\SearchIndexer\SearchIndexerService;
use Shader2k\SearchIndexer\Traits\HelpersTrait;

class IndexerTest extends TestCase
{
    use HelpersTrait;



    public function testGetDataFromModel(): void
    {
        $searchIndexer = new SearchIndexerService(new EloquentProvider(), new ElasticsearchDriver());
        $getChunkOfDataFromModel = self::getProtectedMethod('getChunkOfDataFromModel', get_class($searchIndexer));
        $chunk = $getChunkOfDataFromModel->invokeArgs($searchIndexer, [new User()]);

        $this->assertTrue($chunk);
        $this->assertIsArray($searchIndexer->getData());

    }

    public function testIndexingModel(): void
    {
        $searchIndexer = new SearchIndexerService(new EloquentProvider(), new ElasticsearchDriver());
        $data = [
            ['name' => 'John', 'email' => 'john@example.com'],
            ['name' => 'Artur', 'email' => 'artur@example.com']
        ];
        $index = $searchIndexer->indexingModel(new User());
        //todo тестирование в индексе elasticsearch
        $this->assertTrue($index);

    }

}
