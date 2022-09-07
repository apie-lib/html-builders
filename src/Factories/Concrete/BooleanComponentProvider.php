<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Forms\Checkbox;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Utils;
use ReflectionNamedType;
use ReflectionType;

class BooleanComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool
    {
        return $type instanceof ReflectionNamedType && $type->isBuiltin() && $type->getName() === 'bool';
    }
    public function createComponentFor(ReflectionType $type, ApieContext $context, array $prefix, array $filledIn): ComponentInterface
    {
        // TODO dropdown if nullable boolean
        return new Checkbox(
            Utils::toFormName($prefix),
            $filledIn[end($prefix)] ?? false
        );
    }
}
