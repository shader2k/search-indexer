<?php


namespace Shader2k\SearchIndexer\Traits;


trait LaravelSearchTrait
{

    /**
     * Получение данных из модели
     * @param int $chunk
     * @param int $page
     * @return object
     */
    public function getDataFromModel(int $chunk, int $page): object
    {
        return $this->query()->select(array_merge(['id'], $this->indexFields))->paginate($chunk, ['*'], 'page', $page);
    }


}
