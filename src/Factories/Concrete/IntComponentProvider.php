<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionNamedType;
use ReflectionType;

class IntComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        return $type instanceof ReflectionNamedType && $type->isBuiltin() && $type->getName() === 'int';
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        return new Input(
            $context->getFormName(),
            $context->getFilledInValue($type->allowsNull() ? null : ''),
            'text',
            [
                'inputmode' => 'numeric',
                'pattern' => '[1-9]\d*'
            ],
            $type->allowsNull()
        );
    }
}
