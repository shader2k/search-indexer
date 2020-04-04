# Elasticsearch zero downtime reindexing

Переиндексация модели с нулевым временем простоя

## Установка

Установка через Composer
```
composer config repositories.shader2k vcs https://github.com/shader2k/search-indexer
composer require shader2k/search-indexer
```
## Настройка

Скопировать config файл в `<projectRoot>/config/indexerconfig.php` 

параметры конфигурации:
- `searchDriverFactories` и `dataProviderFactories` - доступные в системе драйвера и провайдеры 
(можно расширять имеющиеся, указав алиасы и классы фабрик)
- `searchDriverNameDefault` и `dataProviderNameDefault` - алиасы драйвера и провайдера по умолчанию
- `dataProviderChunkSize` размер частей, для получения данных из модели
- `elasticsearchHost` хост поискового движка (ElasticSearch)

При необходимости, можно разместить .env файл в корень проекта (.env.example)

## Использование

Индексируемая модель, должна **обязательно** имплементировать интерфейс `\Indexable\IndexableContract`  

Реализуемые методы: 
- `getIndexableFields()` должен вернуть массив имен полей, которые небоходимо добавить в индекс
       
- `getIdentifierField()` должен вернуть имя поля, используемое в качестве ID  

- `getSearchDriverName()` если метод вернет null, будет использован драйвер по умолчанию. Если указать имя драйвера, для индексирования будет использован этот драйвер 

- `getProviderName()` имя провайдера. Используется аналогично, методу `getSearchDriverName()`

- `getIndexName()` имя индекса (должно быть уникально), например `return __CLASS__;`

- `getIdentifierValue()` значение ID, например `return (string) $this->id;`

В пакете присутсвует трейт `Traits\IndexableTrait` который можно использовать в моделях, для использования драйвера и провайдера по умолчанию.


### Использование сервиса в проекте
Инициализировать сервис желательно как синглтон
```php
$searchIndexer = new SearchIndexerService(new ProviderManager(), new DriverManager());
//класс User должен имплементировать интерфейс IndexableContract
$searchIndexer->indexingModel(User::class);
```



### Добавление сервиса в проект на Laravel
В методе `boot()` AppServiceProvider.php нужно объявить сервис как синглтон
```php
$this->app->singleton('SearchIndexerService', function ($app) {
    return new SearchIndexerService(new ProviderManager(), new DriverManager());
});
```
Использование сервиса:
```php
$indexerService = App::make('SearchIndexerService');
//класс User должен имплементировать интерфейс IndexableContract
$indexerService->indexingModel(User::class);
```

# Расширение пакета
Возможно расширение пакета за счет дополнительных драйверов для поисковых движков и провайдеров данных  

Добавление драйвера:  
Необходимо реализовать интерфейс фабрики для драйвера `DriverFactoryContract` и интерфейс драйвера `DriverContract`.
Добавить новый драйвер в config: 
```php
'searchDriverFactories' => [
    'anyAlias' => '\YourRepository\DriverFactoryClass'
]
```
Аналогично, можно расширить провайдеры, реализуя `ProviderFactoryContract` для фабрики и `ProviderContract` для провайдера.
Добавить новый провайдер в config:

```php
'dataProviderFactories' => [
    'anyAlias' => '\YourRepository\ProviderFactoryClass'
]
```
