<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\HtmlBuilders\Components\Resource\FieldDisplay\NullDisplay;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;

final class NullDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return $object === null;
    }
    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        assert(null === $object);
        return new NullDisplay();
    }
}
