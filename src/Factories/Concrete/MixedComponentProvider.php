<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionNamedType;
use ReflectionType;

final class MixedComponentProvider implements FormComponentProviderInterface
{
    /** @var array<string, bool> */
    private array $handled = [];

    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        return $type instanceof ReflectionNamedType && $type->getName() === 'mixed';
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $formName = $context->getFormName();
        $name = (string) $formName;
        $typesAllowed = 'string|int|null|bool|float';
        // avoid infinite recursion by only allowing array if we are not too many levels deep and
        // storing the currently pending form fields.
        if (count($formName->toNative()) < 6) {
            $typesAllowed .= '|array';
            
            foreach (array_keys($this->handled) as $handledName) {
                if (str_starts_with($handledName, $name)) {
                    $typesAllowed = 'string|int|null|bool|float';
                    break;
                }
            }
        }
        try {
            $this->handled[$name] = true;
            return $context->getComponentFactory()->createFromType(
                ReflectionTypeFactory::createReflectionType($typesAllowed),
                $context
            );
        } finally {
            unset($this->handled[$name]);
        }
    }
}