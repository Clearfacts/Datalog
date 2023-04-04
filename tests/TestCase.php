<?php

declare(strict_types=1);

namespace Tests\Datalog;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function callPrivateMethod(&$object, $methodName, ...$params)
    {
        $reflectionObject = new \ReflectionObject($object);
        $method = $reflectionObject->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $params);
    }
}