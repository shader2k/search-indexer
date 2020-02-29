<?php


namespace Shader2k\SearchIndexer\Drivers;


use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriverFactory;

abstract class DriverFactory implements DriverFactoryContract
{
    public static function create(): DriverContract
    {
        $driverName = ucfirst(env('INDEX_DRIVER'));
        $driverClass = __NAMESPACE__ .'\\'.$driverName.'\\'.$driverName.'DriverFactory';
        return (new $driverClass())->getDriver();
    }

    abstract public function getDriver();

}
