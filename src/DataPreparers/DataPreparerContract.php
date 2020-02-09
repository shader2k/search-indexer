<?php

namespace Shader2k\SearchIndexer\DataPreparers;

interface DataPreparerContract
{
    /**
     * @param array $rawData
     * @param array $modelParams
     * @return array
     */
    public function toBulk(array $rawData, array $modelParams): array;
}
