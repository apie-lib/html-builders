<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Utils;
use ReflectionNamedType;
use ReflectionType;

class IntComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool
    {
        return $type instanceof ReflectionNamedType && $type->isBuiltin() && $type->getName() === 'int';
    }
    public function createComponentFor(ReflectionType $type, ApieContext $context, array $prefix, array $filledIn): ComponentInterface
    {
        return new Input(
            Utils::toFormName($prefix),
            $filledIn[end($prefix)] ?? '',
            'text',
            [
                'inputmode' => 'numeric',
                'pattern' => '[1-9]\d*'
            ]
        );
    }
}
