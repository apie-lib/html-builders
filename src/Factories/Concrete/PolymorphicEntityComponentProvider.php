<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Other\DiscriminatorMapping;
use Apie\HtmlBuilders\Components\Forms\FormSplit;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class PolymorphicEntityComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName())) {
            $refl = new ReflectionClass($type->getName());
            return $refl->implementsInterface(PolymorphicEntityInterface::class)
                && $refl->getMethod('getDiscriminatorMapping')->getDeclaringClass()->name === $refl->name;
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
        $method = $refl->getMethod('getDiscriminatorMapping');
        /** @var DiscriminatorMapping $mapping */
        $mapping = $method->invoke(null);
        $propertyName = $mapping->getPropertyName();
        $configs = $mapping->getConfigs();
        $value = $context->createChildContext($propertyName)->getFilledInValue(
            ($type->allowsNull() || empty($configs))
            ? null
            : reset($configs)->getDiscriminator()
        );
        $components = [];
        foreach ($configs as $config) {
            $components[$config->getDiscriminator()] = $formComponentFactory->createFromClass(
                new ReflectionClass($config->getClassName()),
                $context->withApieContext('not-root', true)
            );
        }

        return new FormSplit(
            $context->getFormName()->createChildForm($propertyName),
            isRootObject: !$context->getFormName()->hasChildFormFieldName() && !$context->getApieContext()->getContext('not-root', false),
            isPolymorphic: true,
            value: $value,
            tabComponents: new ComponentHashmap($components)
        );
    }
}
