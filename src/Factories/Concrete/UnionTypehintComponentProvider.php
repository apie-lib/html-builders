<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\Strategy\UnionTypeStrategy;
use Apie\Core\Metadata\UnionTypeMetadata;
use Apie\HtmlBuilders\Components\Forms\FormSplit;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use ReflectionType;
use ReflectionUnionType;

class UnionTypehintComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        return $type instanceof ReflectionUnionType;
    }

    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        assert($type instanceof ReflectionUnionType);
        $formComponentFactory = $context->getComponentFactory();
        $metadata = MetadataFactory::getMetadataStrategyForType($type);
        if ($metadata instanceof UnionTypeStrategy) {
            // TODO handle empty string/null
            $scalar = $metadata->getCreationMetadata($context->getApieContext())->toScalarType(true);
            if (!in_array($scalar, [ScalarType::ARRAY, ScalarType::STDCLASS, ScalarType::MIXED])) {
                return $formComponentFactory->createFromType($scalar->toReflectionType(), $context);
            }
        }
        $components = [];
        foreach ($type->getTypes() as $subType) {
            $key = $this->getSafePanelName($subType);
            $components[$key] = $formComponentFactory->createFromType($subType, $context);
        }
        return new FormSplit($context->getFormName(), $context->getFilledInValue($type->allowsNull() ? null : ''), new ComponentHashmap($components));
    }

    public function getSafePanelName(ReflectionType $type): string
    {
        $type = (string) $type;
        $pos = strrpos($type, '\\');
        if ($pos !== false) {
            $type = substr($type, $pos + 1);
        }
        return preg_replace('/[^A-Za-z0-9]/', '_', $type);
    }
}
