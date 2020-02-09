<?php

namespace Tests;


use Shader2k\SearchIndexer\DataPreparers\ElasticsearchDataPreparer;
use Shader2k\SearchIndexer\Traits\HelpersTrait;

class ElasticsearchDataPreparerTest extends TestCase
{
    use HelpersTrait;

    /**
     * приведение данных в формат Elasticsearch для Bulk
     * @throws \ReflectionException
     */
    public function testPrepareDataToBulk()
    {
        $modelParams = [
            'indexType' => 'App/User',
            'indexAliasWrite' => 'App/User_write',
        ];
        $data = [
            [
                'id' => 5,
                'name' => 'John',
                'email' => 'test@example.com'

            ],
            [
                'id' => 1,
                'name' => 'Alex',
                'email' => 'example@example.com'

            ]
        ];
        $preparedData = [
            "body" => [
                [
                    "index" => [
                        "_index" => "App/User_write",
                        "_type" => "App/User",
                        "_id" => 5
                    ]
                ],
                [
                    "name" => "John",
                    "email" => "test@example.com"
                ],
                [
                    "index" => [
                        "_index" => "App/User_write",
                        "_type" => "App/User",
                        "_id" => 1
                    ]
                ],
                [
                    "name" => "Alex",
                    "email" => "example@example.com"
                ]
            ]

        ];

        $dataPreparer = new ElasticsearchDataPreparer();
        $data = $dataPreparer->toBulk($data, $modelParams);

        $this->assertIsArray($data);
        $this->assertEquals($data, $preparedData);

    }


}
