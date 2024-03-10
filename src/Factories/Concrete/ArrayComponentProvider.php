<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Lists\ItemHashmap;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionNamedType;
use ReflectionType;

final class ArrayComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        return $type instanceof ReflectionNamedType && $type->getName() === 'array';
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        return $context->getComponentFactory()->createFromType(
            ReflectionTypeFactory::createReflectionType(ItemHashmap::class),
            $context
        );
    }
}
