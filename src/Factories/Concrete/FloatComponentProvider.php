<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\MetadataFactory;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionType;

class FloatComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        $metadata = MetadataFactory::getCreationMetadata($type, $context->getApieContext());
        return $metadata->toScalarType() === ScalarType::FLOAT;
    }

    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        return new Input(
            $context->getFormName(),
            $context->getFilledInValue($type->allowsNull() ? null : '', true),
            'number',
            [],
            $type->allowsNull(),
            $context->getValidationError()
        );
    }
}
