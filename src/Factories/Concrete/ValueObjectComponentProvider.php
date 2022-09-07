<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Context\ApieContext;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class ValueObjectComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName()) && $context->hasContext(FormComponentFactory::class)) {
            $refl = new ReflectionClass($type->getName());
            return $refl->implementsInterface(ValueObjectInterface::class);
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
        $method = $refl->getMethod('toNative');
        return $formComponentFactory->createFromType($context, $method->getReturnType(), $prefix, $filledIn);
    }
}
