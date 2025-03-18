<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\Core\Utils\ValueObjectUtils;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;
use ReflectionClass;

final class ValueObjectDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return $object instanceof ValueObjectInterface && ValueObjectUtils::isNonCompositeValueObject(new ReflectionClass($object));
    }
    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        assert($object instanceof ValueObjectInterface);
        return $context->createComponentFor($object->toNative());
    }
}
