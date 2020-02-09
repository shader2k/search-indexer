<?php


namespace Shader2k\SearchIndexer\Providers;


class EloquentProvider
{
    /**
     * @param object $model
     * @param $chunk
     * @return array
     */
    public function getChunk(object $model, $chunk): array
    {
        $response = $model->getDataFromModel($chunk)->toArray();
        return [
            'lastPage' => $response['current_page'] < $response['last_page']? false : true,
            'data' => $response['data']
        ];
    }
}
