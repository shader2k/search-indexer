<?php


namespace Shader2k\SearchIndexer\Providers;


use Shader2k\SearchIndexer\Exceptions\ProviderException;
use Shader2k\SearchIndexer\Helpers\Helper;

class ProviderManager
{
    private $providers;

    /**
     * Возвращает или создает провайдер
     * @param string $providerName
     * @return ProviderContract
     * @throws ProviderException
     */
    public function getProvider(string $providerName = null): ProviderContract
    {
        $providerName = $providerName ?: $this->getDefaultProviderName();
        if ($this->providers[$providerName] === null) {
            $this->providers[$providerName] = $this->buildProvider(
                $this->createProviderFactory(config('indexerconfig.dataProviderFactories.' . $providerName))
            );
        }
        return $this->providers[$providerName];
    }

    private function getDefaultProviderName(): string
    {
        return config('indexerconfig.dataProviderNameDefault');
    }

    /**
     * Создать драйвер
     * @param ProviderFactoryContract $providerFactory
     * @return ProviderContract
     */
    private function buildProvider(ProviderFactoryContract $providerFactory): ProviderContract
    {
        return (new $providerFactory())->buildProvider();

    }

    /**
     * Создание фабрики провайдера
     * @param string $providerClass
     * @return ProviderFactoryContract
     */
    private function createProviderFactory(string $providerClass): ProviderFactoryContract
    {
        Helper::classExists($providerClass, ProviderException::class);
        Helper::classImplement($providerClass, ProviderFactoryContract::class, ProviderException::class);
        return new $providerClass();

    }


}
