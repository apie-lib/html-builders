<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\CompositeValueObjects\CompositeValueObject;
use Apie\Core\ReflectionTypeFactory;
use Apie\HtmlBuilders\Components\Forms\FormGroup;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

/**
 * Creates a form field for composite value objects.
 */
class CompositeValueObjectComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            $refl = new ReflectionClass($type->getName());
            return in_array(CompositeValueObject::class, $refl->getTraitNames());
        }
        return false;
    }

    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        assert($type instanceof ReflectionNamedType);
        $refl = new ReflectionClass($type->getName());
        /** @var array<string, FieldInterface> $fields */
        $fields = $refl->getMethod('getFields')->invoke(null);

        $components = [];

        $componentFactory = $context->getComponentFactory();
        foreach ($fields as $name => $field) {
            $components[] = $componentFactory->createFromType(
                ReflectionTypeFactory::createReflectionType($field->getTypehint()),
                $context->createChildContext($name)
            );
        }
        return new FormGroup(
            $context->getFormName(),
            ...$components
        );
    }
}
