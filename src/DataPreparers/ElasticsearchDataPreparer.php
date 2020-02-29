<?php


namespace Shader2k\SearchIndexer\DataPreparers;


class ElasticsearchDataPreparer implements DataPreparerContract
{

    /**
     * @param array $rawData
     * @param array $modelParams
     * @return array
     */
    public function toBulk(array $rawData, array $modelParams): array
    {
        if (empty($rawData)) {
            return [];
        }
        $preparedData = [];
        foreach ($rawData as $row) {
            $preparedData['body'][] = [
                'index' => [
                    '_index' => $modelParams['indexAliasWrite'],
                    '_type' => $modelParams['indexType'],
                    '_id' => $row['id'],
                ],
            ];
            unset($row['id']);
            $preparedData['body'][] = $row;
        }

        return $preparedData;
    }


}
