<?php


namespace Shader2k\SearchIndexer\Traits;


trait LaravelSearchTrait
{

    public function getDataFromModel($chunk): object
    {
        return $this->query()->select(array_merge(['id', $this->indexFields]))->paginate($chunk);
    }


}
