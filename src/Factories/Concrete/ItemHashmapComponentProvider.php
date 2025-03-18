<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Utils\HashmapUtils;
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
        if (HashmapUtils::isHashmap($type)) {
            assert($type instanceof ReflectionNamedType);
            $refl = new ReflectionClass($type->getName());
            return $refl->isInstantiable();
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
        $prototypeName = $context->getFormName()->getPrototypeName();
        return new FormPrototypeHashmap(
            $context->getFormName(),
            $context->getFilledInValue($type->allowsNull() ? null : []),
            $prototypeName,
            $formComponentFactory->createFromType(
                $refl->getMethod('offsetGet')->getReturnType(),
                $context->createChildContext($prototypeName)
            )
        );
    }
}
