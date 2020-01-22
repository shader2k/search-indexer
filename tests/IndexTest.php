<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Shader2k\SearchIndexer\SearchIndexerService;
use Shader2k\SearchIndexer\Traits\HelpersTrait;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use HelpersTrait;
    public function testGetDataFromModel()
    {
        $searchIndexer = new SearchIndexerService();
        //$searchIndexer->indexingModel('App\User');
        $getChunkOfDataFromModel = self::getProtectedMethod('getChunkOfDataFromModel', get_class($searchIndexer));
        $chunk = $getChunkOfDataFromModel->invokeArgs($searchIndexer, ['App\User']);

        $this->assertTrue($chunk);
        $this->assertIsArray($searchIndexer->getData());

    }

    public function testIndex()
    {
        $searchIndexer = new SearchIndexerService();
        $data = [
            ['name' => 'John', 'email' => 'john@example.com'],
            ['name' => 'Artur', 'email' => 'artur@example.com']
        ];
        $searchIndexer->setData($data);
        $indexing = self::getProtectedMethod('indexing', get_class($searchIndexer));
        $data = $indexing->invokeArgs($searchIndexer);

        $this->assertTrue($data);

    }

}
