<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\Core\Lists\ItemHashmap;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\SegmentDisplay;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;

final class ArrayDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return is_array($object) && $object instanceof ItemHashmap;
    }
    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        assert(is_array($object) && $object instanceof ItemHashmap);
        $detailComponents = [];
        $childContext = $context->createChildContext('_');
        foreach ($object as $key => $value) {
            $detailComponents[$key] = $childContext->createComponentFor($value);
        }
        return new SegmentDisplay($detailComponents);
    }
}
