<?php

namespace Shader2k\SearchIndexer\Tests\Data;

use App\User;
use Exception;
use Mockery as m;
use Shader2k\SearchIndexer\Indexable\IndexableCollection;
use Shader2k\SearchIndexer\Indexable\IndexableCollectionContract;
use Shader2k\SearchIndexer\Indexable\IndexableContract;

class MockObjects
{
    /**
     * Cоздание объекта пользователь
     * @param array $fields
     * @return IndexableContract
     * @throws Exception
     */
    public static function getUserObject(array $fields = []): IndexableContract
    {
        $mockUser = m::mock('alias:' . User::class, IndexableContract::class);
        $mockUser->shouldReceive('getIndexName')
            ->andReturn('fakeIndexName');
        $mockUser->shouldReceive('getSearchDriverName')
            ->andReturn('fakeDriver');
        $mockUser->shouldReceive('getIndexableFields')
            ->andReturn(['name', 'email']);
        if (!empty($fields['id'])) {
            $mockUser->shouldReceive('getIdentifierValue')
                ->andReturn($fields['id']);
        } else {
            $mockUser->shouldReceive('getIdentifierValue')
                ->andReturn(random_int(1, 100));
        }
        $mockUser->shouldReceive('getIdentifierField')
            ->andReturn('id');
        if (!empty($fields)) {
            foreach ($fields as $prop => $val) {
                $mockUser->$prop = $val;
            }
        }
        return $mockUser;
    }

    /**
     * Генерация коллекции
     * @param int $count
     * @param array $fields
     * @return IndexableCollectionContract
     */
    public static function getIndexableCollection(int $count = 1, array $fields = []): IndexableCollectionContract
    {
        $collection = new IndexableCollection();

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $collection->push(factory(User::class)->make($field));
            }
            return $collection;
        }
        for ($i = 0; $i < $count; $i++) {
            $collection->push(factory(User::class)->make(['id' => 1]));
        }
        return $collection;
    }


}
