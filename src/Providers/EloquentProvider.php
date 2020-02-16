<?php


namespace Shader2k\SearchIndexer\Providers;


class EloquentProvider
{
    private $nextPage = 1;
    /**
     * @param object $model
     * @param int $chunk
     * @return array
     */
    public function getChunk(object $model, int $chunk): array
    {
        $response = $model->getDataFromModel($chunk, $this->getNextPage())->toArray();
        if($response['current_page'] > $response['last_page']){
            $this->setNextPage(1);
        }else{
            $nextPage = $response['current_page'] + 1;
            $this->setNextPage($nextPage);
        }
        return $response['data'];
    }

    public function setNextPage(int $page): void
    {
        $this->nextPage = $page;
    }

    public function getNextPage(): int
    {
        return $this->nextPage;
    }
}
