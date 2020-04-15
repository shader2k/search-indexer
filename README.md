![PHPUnit](https://github.com/shader2k/search-indexer/workflows/PHPUnit/badge.svg?branch=master)

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

Параметры конфигурации:
- `searchDriverFactories` и `dataProviderFactories` - доступные в системе драйвера и провайдеры 
(можно расширять имеющиеся, указав алиасы и классы фабрик)
- `searchDriverNameDefault` и `dataProviderNameDefault` - алиасы драйвера и провайдера по умолчанию
- `dataProviderChunkSize` размер частей для получения данных из модели
- `elasticsearchHost` хост поискового движка (ElasticSearch)

При необходимости можно разместить .env файл в корень проекта (.env.example)

## Использование

Индексируемая модель должна **обязательно** имплементировать интерфейс `\Indexable\IndexableContract`  

Реализуемые методы: 
- `getIndexableFields()` должен вернуть массив имен полей, которые небоходимо добавить в индекс
       
- `getIdentifierField()` должен вернуть имя поля, используемое в качестве ID  

- `getSearchDriverName()` если метод вернет null, будет использован драйвер по умолчанию. Если указать имя драйвера, для индексирования будет использован этот драйвер 

- `getProviderName()` имя провайдера. Используется аналогично методу `getSearchDriverName()`

- `getIndexName()` имя индекса (должно быть уникально), например `return __CLASS__;`

- `getIdentifierValue()` значение ID, например `return (string) $this->id;`

В пакете присутствует трейт `Traits\IndexableTrait`, который можно использовать в моделях для использования драйвера и провайдера по умолчанию.


### Использование сервиса в проекте
Инициализировать сервис желательно как синглтон
- `indexingModel()` переиндексация всей модели. В данном случае будет создан новый индекс,
 в него проиндексируется модель, затем, атомарной операцией, старый индекс будет подменен новым.
- `indexingEntity()` добавить одну сущность в индекс
- `removeEntity()` удаление сущности из индекса

```php
$searchIndexer = new SearchIndexerService(new ProviderManager(), new DriverManager());
//переиндексация всей модели
$searchIndexer->indexingModel(User::class);
//переиндексация одной сущности
$index = $searchIndexer->indexingEntity($entity);
//удаление одной сущности
$index = $searchIndexer->removeEntity($entity);
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
//переиндексация всей модели
$indexerService->indexingModel(User::class);
//переиндексация одной сущности
$index = $indexerService->indexingEntity($entity);
//удаление одной сущности
$index = $indexerService->removeEntity($entity);
```

# Расширение пакета
Возможно расширение пакета за счет дополнительных драйверов для поисковых движков (например Sphinx, Elasticsearch) и провайдеров данных (например Eloquent, Doctrine)  

Добавление драйвера:  
Необходимо реализовать интерфейс фабрики для драйвера `DriverFactoryContract` и интерфейс драйвера `DriverContract`.
Добавить новый драйвер в config: 
```php
'searchDriverFactories' => [
    'anyAlias' => '\YourRepository\DriverFactoryClass'
]
```
При реализации метода `prepareIndex` драйвера, необходимо обеспечить бесперебойную работу старого индекса, в случае переиндексации всей модели
(на что указывает параметр `$reindex` со значением `true`). 
После переиндексации - атомарной операцией подменить старый индекс на новый.

Аналогично можно расширить провайдеры, реализуя `ProviderFactoryContract` для фабрики и `ProviderContract` для провайдера.
Добавить новый провайдер в config:

```php
'dataProviderFactories' => [
    'anyAlias' => '\YourRepository\ProviderFactoryClass'
]
```
