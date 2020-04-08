<?php

namespace Shader2k\SearchIndexer\Tests\Data;

use Exception;
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Indexable\IndexableContract;

class MockObjects
{
    /**
     * Генерация коллекции
     * @param int $count
     * @param array $fields
     * @return IndexableCollectionContract
     * @throws Exception
     */
    public static function getIndexableCollection(int $count = 1, array $fields = []): IndexableCollectionContract
    {
        $collection = new IndexableCollection();
        if ($count === 0) {
            return $collection;
        }
        $userRawData = self::getRawUserData($count);
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $collection->push(self::getUserObject($field));
            }
            return $collection;
        }

        for ($i = 0; $i < $count; $i++) {
            $collection->push(self::getUserObject($userRawData[$i]));
        }
        return $collection;
    }


    /**
     * Cоздание объекта пользователь
     * @param array $fields
     * @return IndexableContract
     * @throws Exception
     */
    public static function getUserObject(array $fields = []): IndexableContract
    {
        $user = new UserModel();
        if (empty($fields)) {
            $user->id = random_int(1, 100);
            $user->name = 'fakeUserName';
            $user->email = 'fake@email.com';
            return $user;
        }

        foreach ($fields as $prop => $val) {
            $user->$prop = $val;
        }

        return $user;
    }

    public static function getRawUserData(int $count = 1): array
    {
        $rawData = [
            [
                'id' => 3,
                'name' => 'John',
                'email' => 'john@example.com'

            ],
            [
                'id' => 1,
                'name' => 'Alex',
                'email' => 'alex@example.com'

            ],
            [
                'id' => 2,
                'name' => 'Mike',
                'email' => 'mike@example.com'

            ],
            [
                'id' => 4,
                'name' => 'Tomas',
                'email' => 'tomas@example.com'

            ],
            [
                'id' => 5,
                'name' => 'Martin',
                'email' => 'martin@example.com'

            ]
        ];
        return array_slice($rawData, 0, $count);
    }

}
