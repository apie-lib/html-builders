<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\HtmlBuilders\Components\Resource\FieldDisplay\BooleanDisplay;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;

final class BooleanDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return $object === true || $object === false;
    }
    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        assert(is_bool($object));
        return new BooleanDisplay($object);
    }
}
