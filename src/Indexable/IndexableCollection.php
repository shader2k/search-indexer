<?php


namespace Shader2k\SearchIndexer\Indexable;


class IndexableCollection implements IndexableCollectionContract
{


    /**
     * @var array
     */
    protected $items;
    /**
     * @var string|null
     */
    protected $driver = null;
    /**
     * @var string|null
     */
    protected $indexName = null;

    /**
     * @inheritDoc
     */
    public function __construct(IndaxableContract ...$items)
    {
        if (!empty($items)) {
            $this->items = $this->collect($items);
            $this->driver = $items[0]->getSearchDriver();
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
    public function push(IndaxableContract $item): void
    {
        $this->items[] = new IndexableEntity($item);
        if ($this->driver === null) {
            $this->driver = $item->getSearchDriver();
        }
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
    public function getSearchDriver(): ?string
    {
        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function getIndexName(): ?string
    {
        return $this->indexName;
    }
}
