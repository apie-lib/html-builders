<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Metadata\MetadataFactory;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\SegmentDisplay;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;
use ReflectionClass;

class SegmentDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        if (!is_object($object)) {
            return false;
        }
        $metadata = MetadataFactory::getResultMetadata(
            new ReflectionClass($object),
            $context->getApieContext()
        );
        return $metadata->toScalarType() === ScalarType::STDCLASS;
    }

    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        $metadata = MetadataFactory::getResultMetadata(
            new ReflectionClass($object),
            $context->getApieContext()
        );
        $nodes = $context->getVisitedNodes();
        $prefix = empty($nodes) ? '' : (implode('.', $nodes) . '.');
        $detailComponents = [];
        foreach ($metadata->getHashmap()->filterOnContext($context->getApieContext()) as $propertyName => $fieldMetadata) {
            if ($fieldMetadata instanceof GetterInterface) {
                $propertyContext = $context->createChildContext($propertyName);
                $detailComponents[$prefix . $propertyName] = $propertyContext->createComponentFor(
                    $fieldMetadata->getValue($object, $propertyContext->getApieContext())
                );
            }
        }
        return new SegmentDisplay($detailComponents);
    }
}
