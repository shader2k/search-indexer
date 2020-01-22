<?php


namespace Shader2k\SearchIndexer\Providers;


class EloquentProvider
{
    public function getChunk($modelName, $chunk): array
    {
        $model = new $modelName();
        $response = $model->getDataFromModel($chunk)->toArray();
        return [
            'lastPage' => $response['current_page'] < $response['last_page']? false : true,
            'data' => $response['data']
        ];
    }
}
