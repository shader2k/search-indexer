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

    /**
     * приведение данных в формат Elasticsearch
     * @throws \ReflectionException
     */
    public function testPrepareDataForIndexing(): void
    {
        $searchIndexer = new SearchIndexerService(new EloquentProvider(), new ElasticsearchDriver());
        $getChunkOfDataFromModel = self::getProtectedMethod('checkIndex', get_class($searchIndexer));
        $chunk = $getChunkOfDataFromModel->invokeArgs($searchIndexer, [new User()]);

        $this->assertTrue($chunk);
        $this->assertIsArray($searchIndexer->getData());

    }

    public function testGetDataFromModel(): void
    {
        $searchIndexer = new SearchIndexerService(new EloquentProvider(), new ElasticsearchDriver());
        $getChunkOfDataFromModel = self::getProtectedMethod('getChunkOfDataFromModel', get_class($searchIndexer));
        $chunk = $getChunkOfDataFromModel->invokeArgs($searchIndexer, [new User()]);

        $this->assertTrue($chunk);
        $this->assertIsArray($searchIndexer->getData());

    }

    public function testIndex(): void
    {
        $searchIndexer = new SearchIndexerService(new EloquentProvider(), new ElasticsearchDriver());
        $data = [
            ['name' => 'John', 'email' => 'john@example.com'],
            ['name' => 'Artur', 'email' => 'artur@example.com']
        ];
        $searchIndexer->setData($data);
        $indexing = self::getProtectedMethod('indexing', get_class($searchIndexer));
        $data = $indexing->invokeArgs($searchIndexer,[]);

        $this->assertTrue($data);

    }

}
