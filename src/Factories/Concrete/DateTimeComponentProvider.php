<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\HtmlBuilders\Components\Forms\SingleInput;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use DateTimeInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

/**
 * Renders a date field.
 */
class DateTimeComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            if (class_exists($type->getName()) || interface_exists($type->getName())) {
                $refl = new ReflectionClass($type->getName());
                return $refl->implementsInterface(DateTimeInterface::class);
            }
        }
        return false;
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        return new SingleInput(
            $context->getFormName(),
            $context->getFilledInValue(),
            $context->createTranslationLabel(),
            $type->allowsNull(),
            $type,
            new CmsSingleInput(['datetime'])
        );
    }
}
