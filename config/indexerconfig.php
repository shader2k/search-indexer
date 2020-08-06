<?php

use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriverFactory;
use Shader2k\SearchIndexer\Providers\Eloquent\EloquentProviderFactory;

return [
    'searchDriverFactories' => [
        'elasticsearch' => ElasticsearchDriverFactory::class
    ],
    'searchDriverNameDefault' => 'elasticsearch',

    'dataProviderFactories'   => [
        'eloquent' => EloquentProviderFactory::class
    ],
    'dataProviderNameDefault' => 'eloquent',
    'dataProviderChunkSize'   => env('DATA_PROVIDER_CHUNK_SIZE', 100),

    'elasticsearchHost'     => env('ELASTICSEARCH_HOST', 'localhost'),
    'elasticsearchLogin'    => env('ELASTICSEARCH_LOGIN', ''),
    'elasticsearchPassword' => env('ELASTICSEARCH_PASSWORD', '')

];
