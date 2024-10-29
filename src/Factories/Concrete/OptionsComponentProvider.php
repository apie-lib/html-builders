<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\Dto\CmsInputOption;
use Apie\Core\Metadata\MetadataFactory;
use Apie\HtmlBuilders\Components\Forms\SingleInput;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionType;

class OptionsComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        $metadata = MetadataFactory::getCreationMetadata($type, $context->getApieContext());
        return !empty($metadata->getValueOptions($context->getApieContext(), true)?->toArray());
    }

    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $options = MetadataFactory::getCreationMetadata($type, $context->getApieContext())
            ->getValueOptions($context->getApieContext(), true);
        return new SingleInput(
            $context->getFormName(),
            $context->getFilledInValue(),
            $context->createTranslationLabel(),
            $type->allowsNull(),
            $type,
            new CmsSingleInput(
                ['select'],
                new CmsInputOption(
                    options: $options
                )
            )
        );
    }
}
