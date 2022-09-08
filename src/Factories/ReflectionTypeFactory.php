<?php
namespace Apie\HtmlBuilders\Factories;

use ReflectionClass;
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

    public static function createReflectionType(string $typehint): ReflectionType
    {
        if (strpos($typehint, ';') !== false || strpos($typehint, '/') !== false) {
            throw new RuntimeException('Are you trying to exploit this evil method?');
        }
        if (!isset(self::$alreadyCreated[$typehint])) {
            $fakeClass = eval(
                'return new class { public function method(): ' . $typehint . '{} };'
            );
            $refl = new ReflectionClass($fakeClass);
            self::$alreadyCreated[$typehint] = $refl->getMethod('method')->getReturnType();
        }
        return self::$alreadyCreated[$typehint];
    }
}
