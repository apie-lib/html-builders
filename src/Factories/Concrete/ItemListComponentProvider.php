<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Lists\ItemList;
use Apie\HtmlBuilders\Components\Forms\FormPrototypeList;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class ItemListComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $formBuildContext): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName())) {
            $refl = new ReflectionClass($type->getName());
            return $refl->isInstantiable() && $refl->isSubclassOf(ItemList::class);
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
        return new FormPrototypeList(
            $context->getFormName(),
            $context->getFilledInValue($type->allowsNull() ? null : []),
            $formComponentFactory->createFromType(
                $refl->getMethod('offsetGet')->getReturnType(),
                $context->createChildContext('__PROTO__')
            )
        );
    }
}
