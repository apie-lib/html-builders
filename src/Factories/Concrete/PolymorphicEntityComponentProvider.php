<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Other\DiscriminatorMapping;
use Apie\HtmlBuilders\Components\Forms\FormSplit;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\Utils;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class PolymorphicEntityComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName()) && $context->hasContext(FormComponentFactory::class)) {
            $refl = new ReflectionClass($type->getName());
            return $refl->implementsInterface(PolymorphicEntityInterface::class)
                && $refl->getMethod('getDiscriminatorMapping')->getDeclaringClass()->name === $refl->name;
        }
        return false;
    }

    /**
     * @param ReflectionNamedType $type
     */
    public function createComponentFor(ReflectionType $type, ApieContext $context, array $prefix, array $filledIn): ComponentInterface
    {
        /** @var FormComponentFactory $formComponentFactory */
        $formComponentFactory = $context->getContext(FormComponentFactory::class);
        $refl = new ReflectionClass($type->getName());
        $method = $refl->getMethod('getDiscriminatorMapping');
        /** @var DiscriminatorMapping $mapping */
        $mapping = $method->invoke(null);
        $propertyName = $mapping->getPropertyName();
        $value = $filledIn[$propertyName] ?? reset($mapping->getConfigs());
        $components = [];
        foreach ($mapping->getConfigs() as $config) {
            $components[$config->getDiscriminator()] = $formComponentFactory->createFromClass(
                $context,
                new ReflectionClass($config->getClassName()),
                $prefix,
                $filledIn
            );
        }

        return new FormSplit(Utils::toFormName([...$prefix, $propertyName]), $value, new ComponentHashmap($components));
    }
}
