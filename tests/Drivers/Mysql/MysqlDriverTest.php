<?php

namespace Shader2k\SearchIndexer\Tests\Drivers\Mysql;

use Shader2k\SearchIndexer\Drivers\Mysql\MysqlDriver;
use Shader2k\SearchIndexer\Drivers\Mysql\MysqlRepository;
use Shader2k\SearchIndexer\Tests\Data\UserModel;
use Tests\TestCase;

class MysqlDriverTest extends TestCase
{
    public function testRendexModel(): void
    {
//        $mysqlRepository = m::mock(MysqlRepository::class);
//        $mysqlRepository->shouldReceive('existTable')
//            ->once()
//            ->andReturn(false);
//        $mysqlRepository->shouldReceive('createTable')
//            ->once()
//            ->andReturn(true);
        $driver = new MysqlDriver(new MysqlRepository());
        $response = $driver->prepareIndex(UserModel::class);
        $this->assertTrue($response);
    }

}
