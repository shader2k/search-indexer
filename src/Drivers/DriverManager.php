<?php


namespace Shader2k\SearchIndexer\Drivers;


use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriverFactory;
use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Helpers\Helper;

class DriverManager
{
    private $drivers;


    /**
     * Возвращает или создает дайвер
     * @param $driverName
     * @return DriverContract
     */
    public function getDriver(string $driverName = null): DriverContract
    {
        $driverName = $driverName ?: $this->getDefaultDriverName();
        if ($this->drivers[$driverName] === null) {
            //todo получение класса по имени из конфиг файла
            $this->drivers[$driverName] = $this->buildDriver($this->createDriverFactory(ElasticsearchDriverFactory::class));
        }
        return $this->drivers[$driverName];
    }

    private function getDefaultDriverName(): string
    {   //todo получение имени драйвера по умолчанию из конфиг файла
        return 'elasticsearch';
    }

    /**
     * Создать драйвер
     * @param DriverFactoryContract $driverFactory
     * @return DriverContract
     */
    private function buildDriver(DriverFactoryContract $driverFactory): DriverContract
    {
        /** @var DriverFactoryContract $driverFactory */
        return (new $driverFactory())->buildDriver();

    }

    private function createDriverFactory(string $driverClass): DriverFactoryContract
    {
        Helper::classExists($driverClass, DriverException::class);
        Helper::classImplement($driverClass, DriverFactoryContract::class, DriverException::class);
        return new $driverClass();

    }

}
