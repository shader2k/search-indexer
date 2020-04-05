<?php

use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriverFactory;
use Shader2k\SearchIndexer\Providers\Eloquent\EloquentProviderFactory;

return [
    'searchDriverFactories' => [
        'elasticsearch' => ElasticsearchDriverFactory::class
    ],
    'searchDriverNameDefault' => 'elasticsearch',

    'dataProviderFactories' => [
        'eloquent' => EloquentProviderFactory::class
    ],
    'dataProviderNameDefault' => 'eloquent',
    'dataProviderChunkSize' => env('DATA_PROVIDER_CHUNK_SIZE', 100),

    'elasticsearchHost' => env('ELASTICSEARCH_HOST', 'localhost'),

    //MysqlDriver settings
    'mysqlDriver' => [
        'host' => env('DRIVER_DB_HOST', '127.0.0.1:3306'),
        'db' => env('DRIVER_DATABASE', 'default'),
        'username' => env('DRIVER_DB_USERNAME', 'default'),
        'password' => env('DRIVER_DB_PASSWORD', 'secret'),
        'charset' => env('DRIVER_DB_CHARSET', 'UTF8'),
        'prefix' => env('DRIVER_TABLE_PREFIX', 'index_'),
    ]

];
