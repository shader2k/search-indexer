<?php

return [
    'searchDriverFactories' => [
        'elasticsearch' => 'YourDriver\ElasticsearchDriverFactoryClass'
    ],
    'searchDriverNameDefault' => 'elasticsearch',

    'dataProviderFactories'   => [
        'eloquent' => 'YourProvider\EloquentProviderFactoryClass'
    ],
    'dataProviderNameDefault' => 'eloquent',
    'dataProviderChunkSize'   => env('DATA_PROVIDER_CHUNK_SIZE', 100),

    'elasticsearchHost'     => env('ELASTICSEARCH_HOST', 'localhost'),
    'elasticsearchLogin'    => env('ELASTICSEARCH_LOGIN', ''),
    'elasticsearchPassword' => env('ELASTICSEARCH_PASSWORD', '')

];
