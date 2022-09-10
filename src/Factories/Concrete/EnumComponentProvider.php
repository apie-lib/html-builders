<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\HtmlBuilders\Components\Forms\Select;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Lists\ChoiceList;
use ReflectionEnum;
use ReflectionNamedType;
use ReflectionType;

class EnumComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        return ($type instanceof ReflectionNamedType && !$type->isBuiltin() && enum_exists($type->getName()));
    }

    /**
     * @param ReflectionNamedType $type
     */
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        return new Select(
            $context->getFormName(),
            $context->getFilledInValue(''),
            ChoiceList::createFromEnum(new ReflectionEnum($type->getName()), $type->allowsNull())
        );
    }
}
