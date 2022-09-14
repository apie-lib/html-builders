<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Lists\ItemHashmap;
use Apie\HtmlBuilders\Components\Forms\FormPrototypeHashmap;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class ItemHashmapComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $formBuildContext): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName())) {
            $refl = new ReflectionClass($type->getName());
            return $refl->isInstantiable() && $refl->isSubclassOf(ItemHashmap::class);
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
        return new FormPrototypeHashmap(
            $context->getFormName(),
            $context->getFilledInValue($type->allowsNull() ? null : []),
            $formComponentFactory->createFromType(
                $refl->getMethod('offsetGet')->getReturnType(),
                $context->createChildContext('__PROTO__')
            )
        );
    }
}
