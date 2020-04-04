<?php


namespace Shader2k\SearchIndexer\Drivers;


use Shader2k\SearchIndexer\Exceptions\DriverException;
use Shader2k\SearchIndexer\Helpers\Helper;

class DriverManager
{
    private $drivers;


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
            $driverClass = config('indexerconfig.searchDriverFactories.' . $driverName);
            if (!$driverClass) {
                throw new DriverException('Не указан драйвер поискового движка');
            }
            $this->drivers[$driverName] = $this->buildDriver(
                $this->createDriverFactory(config('indexerconfig.searchDriverFactories.' . $driverName))
            );
        }
        return $this->drivers[$driverName];
    }

    private function getDefaultDriverName(): string
    {
        return config('indexerconfig.searchDriverNameDefault');
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
