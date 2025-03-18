<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\Dto\CmsInputOption;
use Apie\Core\Identifiers\Uuid;
use Apie\Core\Identifiers\UuidV4;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Components\Forms\SingleInput;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionNamedType;
use ReflectionType;

class HideUuidAsIdComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        if (
            !$type instanceof ReflectionNamedType
            || $type->allowsNull()
            || $type->isBuiltIn()
            || !$context->getFormName()->hasChildFormFieldName()
            || $context->getFormName()->getChildFormFieldName() !== 'id'
        ) {
            return false;
        }
        return is_subclass_of($type->getName(), Uuid::class, true);
    }

    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $id = Utils::toString($context->getFilledInValue('', true));
        if (!preg_match(Uuid::getRegularExpression(), $id)) {
            $id = UuidV4::createRandom()->toNative();
        };
        return new SingleInput(
            $context->getFormName(),
            $context->getFilledInValue(),
            $context->createTranslationLabel(),
            $type->allowsNull(),
            $type,
            new CmsSingleInput(
                ['forced_hidden', 'hidden'],
                new CmsInputOption(forcedValue: $id)
            ),
        );
    }
}
