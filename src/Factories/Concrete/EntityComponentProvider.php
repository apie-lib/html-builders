<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class EntityComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName()) && $context->hasContext(FormComponentFactory::class)) {
            $refl = new ReflectionClass($type->getName());
            return $refl->isInstantiable() && $refl->implementsInterface(EntityInterface::class) && !$refl->implementsInterface(PolymorphicEntityInterface::class);
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
        return $formComponentFactory->createFromClass(
            $context,
            new ReflectionClass($type->getName()),
            $prefix,
            $filledIn,
            false
        );
    }
}
