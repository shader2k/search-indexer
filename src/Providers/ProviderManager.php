<?php


namespace Shader2k\SearchIndexer\Providers;


use Shader2k\SearchIndexer\Exceptions\ProviderException;
use Shader2k\SearchIndexer\Helpers\Helper;
use Shader2k\SearchIndexer\Providers\Eloquent\EloquentProviderFactory;

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
            //todo получение класса по имени из конфиг файла
            $this->providers[$providerName] = $this->buildProvider($this->createProviderFactory(EloquentProviderFactory::class));
        }
        return $this->providers[$providerName];
    }

    private function getDefaultProviderName(): string
    {   //todo получение имени провайдера по умолчанию из конфиг файла
        return 'eloquent';
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
