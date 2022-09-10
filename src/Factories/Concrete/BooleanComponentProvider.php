<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Components\Forms\Checkbox;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionNamedType;
use ReflectionType;

/**
 * Creates a form field for a boolean.
 */
class BooleanComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        return $type instanceof ReflectionNamedType && $type->isBuiltin() && $type->getName() === 'bool';
    }

    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $value = $context->getFilledInValue($type->allowsNull() ? null : false);
        if ($value !== null) {
            $value = Utils::toBoolean($value);
        }
        return new Checkbox(
            $context->getFormName(),
            $value,
            $type->allowsNull(),
        );
    }
}
