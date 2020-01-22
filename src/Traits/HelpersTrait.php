<?php


namespace Shader2k\SearchIndexer\Traits;


trait HelpersTrait
{
    /**
     * Позволяет протестировать protected методы
     * @param $name
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    protected static function getProtectedMethod($name, $className)
    {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
