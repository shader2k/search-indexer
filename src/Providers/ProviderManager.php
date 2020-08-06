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
        if (empty($this->providers[$providerName])) {
            $providerClass = config('indexerconfig.dataProviderFactories.' . $providerName);
            if (!$providerClass) {
                throw new ProviderException('Не указан драйвер поискового движка');
            }
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
     * Создать провайдер
     * @param ProviderFactoryContract $providerFactory
     * @return ProviderContract
     */
    private function buildProvider(ProviderFactoryContract $providerFactory): ProviderContract
    {
        return $providerFactory->buildProvider();

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
