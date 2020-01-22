<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Shader2k\SearchIndexer\Drivers\ElasticsearchDriver;
use Shader2k\SearchIndexer\SearchIndexerService;
use Shader2k\SearchIndexer\Traits\HelpersTrait;
use Tests\TestCase;

class ElasticsearchDriverTest extends TestCase
{
    use HelpersTrait;

    public function testPrepareDataBulk()
    {
        $elasticsearchDriver = new ElasticsearchDriver();
        $elasticsearchDriver->setModel('App\User');
        $rawData = [
            [
                'id' => 5,
                'name' => 'name1',
                'email' => 'email@email.com'
            ],
            [
                'id' => 1,
                'name' => 'name2',
                'email' => 'email2@example.com'
            ]
        ];
        $prepareDataBulk = self::getProtectedMethod('prepareDataBulk', get_class($elasticsearchDriver));
        $data = $prepareDataBulk->invokeArgs($elasticsearchDriver, [$rawData]);
        $preparedData = [
            "body" => [
                   [
                    "index" => [
                        "_index" => "App\User_write",
                        "_type" => "App\User",
                        "_id" => 5
                  ]
                ],
                [
                    "name" => "name1",
                    "email" => "email@email.com"
                ],
                [
                    "index" =>[
                    "_index" => "App\User_write",
                    "_type" => "App\User",
                    "_id" => 1
                  ]
                ],
                [
                    "name" => "name2",
                    "email" => "email2@example.com"
                ]
              ]

        ];
        $this->assertIsArray($data);
        $this->assertEquals($data,$preparedData);

    }





}
