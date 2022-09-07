<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Context\ApieContext;
use Apie\Core\Dto\DtoInterface;
use Apie\HtmlBuilders\Components\Forms\FormGroup;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Utils;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;

class DtoComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool
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
    public function createComponentFor(ReflectionType $type, ApieContext $context, array $prefix, array $filledIn): ComponentInterface
    {
        /** @var FormComponentFactory $formComponentFactory */
        $formComponentFactory = $context->getContext(FormComponentFactory::class);
        $refl = new ReflectionClass($type->getName());
        $components = [];
        foreach ($refl->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $newPrefix = [...$prefix, $property->getName()];
            $filledIn = $filledIn[$property->getName()] ?? [];
            $components[] = $formComponentFactory->createFromType($context, $property->getType(), $newPrefix, $filledIn);
        }
        return new FormGroup(Utils::toFormName($prefix), ...$components);
    }
}
