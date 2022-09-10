<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class ValueObjectComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName())) {
            $refl = new ReflectionClass($type->getName());
            return $refl->implementsInterface(ValueObjectInterface::class);
        }
        return false;
    }

    /**
     * @param ReflectionNamedType $type
     */
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $formComponentFactory = $context->getComponentFactory();
        $refl = new ReflectionClass($type->getName());
        $method = $refl->getMethod('toNative');
        return $formComponentFactory->createFromType($method->getReturnType(), $context);
    }
}
