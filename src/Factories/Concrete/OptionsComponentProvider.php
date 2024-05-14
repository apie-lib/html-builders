<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Metadata\MetadataFactory;
use Apie\HtmlBuilders\Components\Forms\Select;
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
        return new Select(
            $context->getFormName(),
            $context->getFilledInValue('', true),
            MetadataFactory::getCreationMetadata($type, $context->getApieContext())
                ->getValueOptions($context->getApieContext(), true)
        );
    }
}
