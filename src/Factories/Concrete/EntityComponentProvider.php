<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\MetadataFactory;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class EntityComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName())) {
            $refl = new ReflectionClass($type->getName());
            return $refl->isInstantiable()
                && !$refl->implementsInterface(PolymorphicEntityInterface::class)
                && MetadataFactory::getCreationMetadata($type, $context->getApieContext()) instanceof CompositeMetadata
            ;
        }
        return false;
    }

    /**
     * @param ReflectionNamedType $type
     */
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $formComponentFactory = $context->getComponentFactory();
        return $formComponentFactory->createFromClass(
            new ReflectionClass($type->getName()),
            $context,
            false
        );
    }
}
