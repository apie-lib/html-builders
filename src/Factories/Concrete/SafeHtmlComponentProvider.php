<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\CommonValueObjects\SafeHtml;
use Apie\HtmlBuilders\Components\Forms\HtmlField;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionNamedType;
use ReflectionType;

class SafeHtmlComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        return $type instanceof ReflectionNamedType && $type->getName() === SafeHtml::class;
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        return new HtmlField(
            $context->getFormName(),
            $context->getFilledInValue($type->allowsNull() ? null : '', true),
            $type->allowsNull(),
            $context->getValidationError()
        );
    }
}