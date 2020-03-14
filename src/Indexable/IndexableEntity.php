<?php


namespace Shader2k\SearchIndexer\Indexable;


class IndexableEntity implements indexableEntityContract
{
    /**
     * @var $indexData array
     */
    private $indexData;

    /**
     * @inheritDoc
     */
    public function __construct(IndaxableContract $item)
    {
        $this->setIndexData($item);
    }

    /**
     * Инициализация данных
     * @param $item
     */
    private function setIndexData($item): void
    {
        $this->indexData = [
            'data' => $this->filterIndexFields($item),
            'identifier' => $item->getIdentifier()
        ];
    }

    /**
     * Фильтрация данных
     * @param $item
     * @return array
     */
    private function filterIndexFields($item): array
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
    public function getIndexData(): array
    {
        return $this->indexData;
    }
}
