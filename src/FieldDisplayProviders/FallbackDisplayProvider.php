<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;

class FallbackDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return true;
    }

    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        return new RawContents(htmlspecialchars(Utils::toString($object)));
    }
}
