<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Utils\HashmapUtils;
use Apie\HtmlBuilders\Components\Forms\FormPrototypeList;
use Apie\HtmlBuilders\Components\Forms\MultiSelect;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class MultiSelectComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $formBuildContext): bool
    {
        if (HashmapUtils::isSet($type)) {
            $metadata = MetadataFactory::getCreationMetadata($type, $formBuildContext->getApieContext());
            $options = $metadata->getArrayItemType()?->getValueOptions($formBuildContext->getApieContext(), true);
            if (empty($options)) {
                return false;
            }
            $scalar = $metadata->getArrayItemType()?->toScalarType();
            if (!in_array($scalar, ScalarType::PRIMITIVES, true)) {
                return false;
            }

            assert($type instanceof ReflectionNamedType);
            $refl = new ReflectionClass($type->getName());
            return $refl->isInstantiable();
        }
        return false;
    }

    /**
     * @param ReflectionNamedType $type
     */
    public function createComponentFor(ReflectionType $type, FormBuildContext $formBuildContext): ComponentInterface
    {
        $metadata = MetadataFactory::getCreationMetadata($type, $formBuildContext->getApieContext());
        $options = $metadata->getArrayItemType()->getValueOptions($formBuildContext->getApieContext(), true);
        return new MultiSelect(
            $formBuildContext->getFormName(),
            $formBuildContext->getFilledInValue($type->allowsNull() ? null : []),
            $options
        );
    }
}
