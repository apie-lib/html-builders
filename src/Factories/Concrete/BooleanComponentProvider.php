<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\Dto\CmsInputOption;
use Apie\Core\Dto\ValueOption;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\ValueOptionList;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Components\Forms\SingleInput;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionType;

/**
 * Creates a form field for a boolean.
 */
class BooleanComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        $metadata = MetadataFactory::getCreationMetadata($type, $context->getApieContext());
        return $metadata->toScalarType() === ScalarType::BOOLEAN;
    }

    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $value = $context->getFilledInValue($type->allowsNull() ? null : false);
        if ($value !== null) {
            $value = Utils::toBoolean($value);
        }
        return new SingleInput(
            $context->getFormName(),
            $value,
            $context->createTranslationLabel(),
            $type->allowsNull(),
            $type,
            new CmsSingleInput(
                $type->allowsNull() ? ['select'] : ['checkbox', 'select'],
                new CmsInputOption(
                    options: new ValueOptionList([
                        new ValueOption('On', true),
                        new ValueOption('Off', false),
                        ...($type->allowsNull() ? [new ValueOption('-', null)] : [])
                    ])
                )
            )
        );
    }
}
