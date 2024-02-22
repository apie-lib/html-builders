<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionNamedType;
use ReflectionType;

final class MixedComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        return $type instanceof ReflectionNamedType && $type->getName() === 'mixed';
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        return $context->getComponentFactory()->createFromType(
            ReflectionTypeFactory::createReflectionType('string|int|array|null|bool|float'),
            $context
        );
    }
}