<?php

namespace Tests\Drivers;

use Exception;
use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDataPreparer;
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Tests\Data\MockObjects;
use Tests\TestCase;

class ElasticsearchDataPreparerTest extends TestCase
{

    /**
     * приведение данных в формат Elasticsearch для Bulk
     * @throws Exception
     */
    public function testPrepareDataToBulk(): void
    {
        $modelParams = [
            'indexType' => 'App/User',
            'indexAliasWrite' => 'user_write',
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
            'body' => [
                [
                    'index' => [
                        '_index' => 'user_write',
                        '_type' => 'App/User',
                        '_id' => 5
                    ]
                ],
                [
                    'name' => 'John',
                    'email' => 'test@example.com'
                ],
                [
                    'index' => [
                        '_index' => 'user_write',
                        '_type' => 'App/User',
                        '_id' => 1
                    ]
                ],
                [
                    'name' => 'Alex',
                    'email' => 'example@example.com'
                ]
            ]

        ];

        $collection = new IndexableCollection();
        $mockUser = MockObjects::getUserObject($data[0]);
        $collection->push($mockUser);
        $mockUser = MockObjects::getUserObject($data[1]);
        $collection->push($mockUser);
        $dataPreparer = new ElasticsearchDataPreparer();
        $data = $dataPreparer->toBulk($collection, $modelParams);

        $this->assertIsArray($data);
        $this->assertEquals($data, $preparedData);

    }


}
