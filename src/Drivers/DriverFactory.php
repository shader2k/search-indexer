<?php


namespace Shader2k\SearchIndexer\Drivers;


abstract class DriverFactory implements DriverFactoryContract
{
    /**
     * Фабричный метод
     * @return DriverContract
     */
    public static function create(): DriverContract
    {
        $driverName = ucfirst(env('INDEX_DRIVER'));
        $driverClass = __NAMESPACE__ . '\\' . $driverName . '\\' . $driverName . 'DriverFactory';
        return (new $driverClass())->getDriver();
    }

    /**
     * Получение экземпляра драйвера
     * @return DriverContract
     */
    abstract public function getDriver(): DriverContract;

}
