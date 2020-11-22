<?php

namespace Shader2k\SearchIndexer\Drivers\Elasticsearch;

use Elasticsearch\ClientBuilder;
use Shader2k\SearchIndexer\Contracts\Drivers\DriverContract;
use Shader2k\SearchIndexer\Contracts\Drivers\DriverFactoryContract;

class ElasticsearchDriverFactory implements DriverFactoryContract
{

    /**
     * Получение экземпляра драйвера
     * @return \Shader2k\SearchIndexer\Contracts\Drivers\DriverContract
     */
    public function buildDriver(): DriverContract
    {
        $dataPreparer = new ElasticsearchDataPreparer();
        //todo проверка на существование env параметра
        $builder = ClientBuilder::create()->setHosts([config('indexerconfig.elasticsearchHost')]);
        $login = config('indexerconfig.elasticsearchLogin');

        if (!empty($login)) {
            $builder->setBasicAuthentication($login, config('indexerconfig.elasticsearchPassword'));
        }
        return new ElasticsearchDriver($dataPreparer, $builder->build());
    }
}
