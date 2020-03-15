<?php


namespace Shader2k\SearchIndexer\Traits;


use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

trait HelpersTrait
{
    /**
     * Позволяет протестировать protected методы
     * @param $name
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    protected static function getProtectedMethod($name, $className)
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * getPrivateProperty
     *
     * @param string $className
     * @param string $propertyName
     * @return    ReflectionProperty
     * @throws ReflectionException
     */
    public function getPrivateProperty($propertyName, $className)
    {
        $reflector = new ReflectionClass($className);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }
}
