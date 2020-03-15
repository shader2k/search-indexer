<?php

namespace Tests;


use App\User;
use ReflectionException;
use Shader2k\SearchIndexer\DataPreparers\ElasticsearchDataPreparer;
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Traits\HelpersTrait;

class ElasticsearchDataPreparerTest extends TestCase
{
    use HelpersTrait;

    /**
     * приведение данных в формат Elasticsearch для Bulk
     * @throws ReflectionException
     */
    public function testPrepareDataToBulk()
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
        $user = factory(User::class)->make($data[0]);
        $collection->push($user);
        $user = factory(User::class)->make($data[1]);
        $collection->push($user);
        $dataPreparer = new ElasticsearchDataPreparer();
        $data = $dataPreparer->toBulk($collection, $modelParams);

        $this->assertIsArray($data);
        $this->assertEquals($data, $preparedData);

    }


}
