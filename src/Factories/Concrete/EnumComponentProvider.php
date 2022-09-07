<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Forms\Select;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Lists\ChoiceList;
use Apie\HtmlBuilders\Utils;
use ReflectionEnum;
use ReflectionNamedType;
use ReflectionType;

class EnumComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool
    {
        return ($type instanceof ReflectionNamedType && !$type->isBuiltin() && enum_exists($type->getName()));
    }
    public function createComponentFor(ReflectionType $type, ApieContext $context, array $prefix, array $filledIn): ComponentInterface
    {
        return new Select(
            Utils::toFormName($prefix),
            $filledIn[end($prefix)] ?? '',
            ChoiceList::createFromEnum(new ReflectionEnum($type->getName()), $type->allowsNull())
        );
    }
}
