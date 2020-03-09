<?php


namespace Shader2k\SearchIndexer\Drivers;


use Shader2k\SearchIndexer\Drivers\Elasticsearch\ElasticsearchDriverFactory;
use Shader2k\SearchIndexer\Exceptions\DriverException;

class DriverManager
{
    private $drivers;

    /**
     * Setter
     * @param string $driverName
     * @param DriverContract $driver must be instance
     */
    public function setDriver(string $driverName, DriverContract $driver): void
    {
        $this->drivers[$driverName] = $driver->buildDriver();
    }

    /**
     * Возвращает или создает дайвер
     * @param $driverName
     * @return DriverContract
     * @throws DriverException
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
        return (new $driverFactory())->buildDriver();

    }

    private function createDriverFactory(string $driverClass): DriverFactoryContract
    {
        $this->classExists($driverClass);
        $this->classImplement($driverClass);
        return new $driverClass();

    }

    /**
     * Проверка на существование класса
     * @param $driverClass
     * @throws DriverException
     */
    private function classExists($driverClass): void
    {
        if (!class_exists($driverClass)) {
            throw new DriverException("{$driverClass} doesn't exist");
        }
    }

    /**
     * Проверка на реализацию интерфейса драйвера
     * @param $class
     * @throws DriverException
     */
    private function classImplement($class): void
    {
        if (false === is_subclass_of($class, DriverFactoryContract::class)) {
            throw new DriverException("{$class} does not implement " . DriverFactoryContract::class);
        }
    }

}
