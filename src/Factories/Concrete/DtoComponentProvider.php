<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Dto\DtoInterface;
use Apie\HtmlBuilders\Components\Forms\FormGroup;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;

class DtoComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName())) {
            $refl = new ReflectionClass($type->getName());
            return $refl->implementsInterface(DtoInterface::class);
        }
        return false;
    }

    /**
     * @param ReflectionNamedType $type
     */
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        @trigger_error(__CLASS__ .  ' is deprecated, use EntityComponentProvider instead', E_USER_DEPRECATED);
        $formComponentFactory = $context->getComponentFactory();
        $refl = new ReflectionClass($type->getName());
        $components = [];
        foreach ($refl->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $childContext = $context->createChildContext($property->getName());
            $components[] = $formComponentFactory->createFromType($property->getType(), $childContext);
        }
        return new FormGroup($context->getFormName(), ...$components);
    }
}
