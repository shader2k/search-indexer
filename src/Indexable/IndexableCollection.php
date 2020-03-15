<?php


namespace Shader2k\SearchIndexer\Indexable;


use ArrayIterator;
use IteratorAggregate;
use Traversable;

class IndexableCollection implements IndexableCollectionContract, IteratorAggregate
{


    /**
     * @var array
     */
    protected $items;
    /**
     * @var string|null
     */
    protected $indexName = null;

    /**
     * @inheritDoc
     */
    public function __construct(IndexableContract ...$items)
    {
        if (!empty($items)) {
            $this->items = $this->collect($items);
            $this->indexName = $items[0]->getIndexName();
        }

    }

    /**
     * Создает массив сущностей для индексирования
     * @param $items
     * @return array
     */
    private function collect($items): array
    {
        $filtered = [];
        foreach ($items as $item) {
            $filtered[] = new IndexableEntity($item);
        }

        return $filtered;
    }


    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }


    /**
     * @inheritDoc
     */
    public function push(IndexableContract $item): void
    {
        $this->items[] = new IndexableEntity($item);
        if ($this->indexName === null) {
            $this->indexName = $item->getIndexName();
        }
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $this->items;
    }


    /**
     * @inheritDoc
     */
    public function getIndexName(): ?string
    {
        return $this->indexName;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
