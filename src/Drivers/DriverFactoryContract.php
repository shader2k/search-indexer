<?php

namespace Shader2k\SearchIndexer\Drivers;

interface DriverFactoryContract
{
    public static function create();

    public function getDriver();
}
