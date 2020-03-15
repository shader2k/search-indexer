<?php


namespace Shader2k\SearchIndexer\Helpers;


use Exception;

class Helper
{

    /**
     * Проверка на существование класса
     * @param $providerClass
     * @param string $exception
     */
    public static function classExists($providerClass, $exception = Exception::class): void
    {
        if (!class_exists($providerClass)) {
            throw new $exception("{$providerClass} doesn't exist");
        }
    }

    /**
     * Проверка на реализацию интерфейса у класса
     * @param $class
     * @param $implement
     * @param string $exception
     */
    public static function classImplement($class, $implement, $exception = Exception::class): void
    {
        if (false === is_subclass_of($class, $implement)) {
            throw new $exception("{$class} does not implement " . $implement);
        }
    }
}
