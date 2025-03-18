<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\MetadataFactory;
use Apie\HtmlBuilders\Components\Forms\SingleInput;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionType;

class IntComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        $metadata = MetadataFactory::getCreationMetadata($type, $context->getApieContext());
        return $metadata->toScalarType() === ScalarType::INTEGER;
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        return new SingleInput(
            $context->getFormName(),
            $context->getFilledInValue(),
            $context->createTranslationLabel(),
            $type->allowsNull(),
            $type,
            new CmsSingleInput(
                ['integer', 'number', 'text']
            )
        );
    }
}
