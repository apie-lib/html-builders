<?php
namespace Apie\HtmlBuilders\Factories;

use ReflectionClass;
use ReflectionMethod;
use ReflectionType;
use RuntimeException;

/**
 * This code is evil, do not reuse!
 *
 * @internal
 */
final class ReflectionTypeFactory
{
    private static array $alreadyCreated = [];

    private static function dummy(): string|false|null
    {
        return null;
    }

    private static function createNullType(): ReflectionType
    {
        $refl = new ReflectionMethod(__CLASS__, 'dummy');
        return $refl->getReturnType()->getTypes()[2];
    }

    private static function createFalseType(): ReflectionType
    {
        $refl = new ReflectionMethod(__CLASS__, 'dummy');
        return $refl->getReturnType()->getTypes()[1];
    }

    public static function createReflectionType(string $typehint): ReflectionType
    {
        if (strpos($typehint, ';') !== false || strpos($typehint, '/') !== false) {
            throw new RuntimeException('Are you trying to exploit this evil method?');
        }
        if (!isset(self::$alreadyCreated[$typehint])) {
            if ($typehint === 'null') {
                self::$alreadyCreated[$typehint] = self::createNullType();
            } elseif ($typehint === 'false') {
                self::$alreadyCreated[$typehint] = self::createFalseType();
            } else {
                $fakeClass = eval(
                    'return new class { public function method(): ' . $typehint . '{} };'
                );
                $refl = new ReflectionClass($fakeClass);
                self::$alreadyCreated[$typehint] = $refl->getMethod('method')->getReturnType();
            }
        }
        return self::$alreadyCreated[$typehint];
    }
}
