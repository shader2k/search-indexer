<?php


namespace Shader2k\SearchIndexer\Traits;


trait LaravelSearchTrait
{

    /**
     * Получение данных из модели
     * @param int $chunk
     * @return object
     */
    public function getDataFromModel(int $chunk): object
    {
        return $this->query()->select(array_merge(['id'], $this->indexFields))->paginate($chunk);
    }


}
