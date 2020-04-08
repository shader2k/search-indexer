<?php


namespace Shader2k\SearchIndexer\Helpers;


use RuntimeException;

class Singleton
{
    private static $instances = [];

    protected function __construct()
    {
    }

    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static;
        }
        return self::$instances[$subclass];
    }

    public function __wakeup()
    {
        throw new RuntimeException('Cannot unserialize singleton');
    }

    protected function __clone()
    {
    }
}
