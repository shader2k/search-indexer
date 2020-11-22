<?php

namespace Shader2k\SearchIndexer\Indexable;

use Shader2k\SearchIndexer\Contracts\Indexable\IndexableContract;
use Shader2k\SearchIndexer\Contracts\Indexable\IndexableEntityContract;

class IndexableEntity implements IndexableEntityContract
{
    /**
     * @var $data array
     */
    private $data;

    private $identifier;

    /**
     * @inheritDoc
     */
    public function __construct(IndexableContract $item)
    {
        $this->setIndexData($item);
    }

    /**
     * Инициализация данных
     * @param IndexableContract $item
     */
    private function setIndexData(IndexableContract $item): void
    {
        $this->data = $this->filterIndexFields($item);
        $this->identifier = $item->getIdentifierValue();

    }

    /**
     * Фильтрация данных
     * @param IndexableContract $item
     * @return array
     */
    private function filterIndexFields(IndexableContract $item): array
    {
        $tmp = [];
        foreach ($item->getIndexableFields() as $filter) {
            $tmp[$filter] = $item->$filter;
        }
        return $tmp;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
