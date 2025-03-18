<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\CommonValueObjects\SafeHtml;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;

final class SafeHtmlDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return $object instanceof SafeHtml;
    }

    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        assert($object instanceof SafeHtml);
        return new RawContents($object);
    }
}
