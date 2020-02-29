<?php

namespace Tests;

use App\User;
use Elasticsearch\ClientBuilder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriver;
use Shader2k\SearchIndexer\Providers\EloquentProvider;
use Shader2k\SearchIndexer\SearchIndexerService;
use Shader2k\SearchIndexer\Traits\HelpersTrait;

class IndexerTest extends TestCase
{
    use HelpersTrait;
    use DatabaseMigrations;



//    public function testGetDataFromModel(): void
//    {
//        $searchIndexer = new SearchIndexerService(new EloquentProvider(), new ElasticsearchDriver());
//        $getChunkOfDataFromModel = self::getProtectedMethod('getChunkOfDataFromModel', get_class($searchIndexer));
//        $chunk = $getChunkOfDataFromModel->invokeArgs($searchIndexer, [new User()]);
//
//        $this->assertTrue($chunk);
//        $this->assertIsArray($searchIndexer->getData());
//
//    }

    public function testIndexingModel(): void
    {
        $searchIndexer = new SearchIndexerService(new EloquentProvider());

        factory(User::class)->create(['name'=>'John', 'email' => 'john@example.com']);
        factory(User::class)->create();

        $index = $searchIndexer->indexingModel(new User());
        //todo тестирование в индексе elasticsearch

        $this->assertTrue($index);

        $params = [
            'index' => 'user_read',
            'body' => [
                'query' => [
                    'match' => [
                        'name' => 'John',
                    ],
                ],
            ],
        ];

        sleep(1);
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();
        $response = $client->search($params);
        $this->assertEquals($response['hits']['hits'][0]['_source']['name'], 'John');
        $this->assertEquals($response['hits']['hits'][0]['_source']['email'], 'john@example.com');


    }

}
