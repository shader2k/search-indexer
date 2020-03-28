<?php


namespace Shader2k\SearchIndexer\Helpers;


use Exception;

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
        throw new Exception("Cannot unserialize singleton");
    }

    protected function __clone()
    {
    }
}
