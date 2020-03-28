<?php


namespace Shader2k\SearchIndexer\Providers\Eloquent;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Providers\ProviderContract;

class EloquentProvider implements ProviderContract
{
    private $nextPage = 1;

    /**
     * @inheritDoc
     */
    public function getChunk(string $modelClass, int $chunkSize): IndexableCollectionContract
    {
        $queryBuilder = $this->getBuilder($modelClass);
        /** @var LengthAwarePaginator $result */
        $result = $queryBuilder->select(array_merge([$modelClass::getIdentifierField()], $modelClass::getIndexableFields()))
            ->paginate($chunkSize, ['*'], 'page', $this->getNextPage());

        if ($result->currentPage() > $result->lastPage()) {
            $this->setNextPage(1);
        } else {
            $nextPage = $result->currentPage() + 1;
            $this->setNextPage($nextPage);
        }
        return new IndexableCollection(...$result->all());
    }

    private function getBuilder(string $modelClass): Builder
    {
        /**  @var Model $modelClass */
        return $modelClass::query();
    }

    /**
     * @inheritDoc
     */
    public function getNextPage(): int
    {
        return $this->nextPage;
    }

    /**
     * @inheritDoc
     */
    public function setNextPage(int $page): void
    {
        $this->nextPage = $page;
    }
}
