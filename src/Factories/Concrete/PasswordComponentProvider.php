<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\ValueObjects\IsPasswordValueObject;
use Apie\HtmlBuilders\Components\Forms\Password;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

/**
 * Renders a password field.
 */
class PasswordComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            if (class_exists($type->getName())) {
                $refl = new ReflectionClass($type->getName());
                return in_array(IsPasswordValueObject::class, $refl->getTraitNames());
            }
        }
        return false;
    }

    /**
     * @param ReflectionNamedType $type
     */
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $value = $context->getFilledInValue($type->allowsNull() ? null : '');

        return new Password(
            $type->getName(),
            $context->getFormName(),
            $value,
            $type->allowsNull()
        );
    }
}
