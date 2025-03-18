<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use BackedEnum;
use UnitEnum;

final class EnumDisplayProvider extends FallbackDisplayProvider
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return $object instanceof UnitEnum;
    }
    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        assert($object instanceof UnitEnum);
        return parent::createComponentFor($object instanceof BackedEnum ? $object->value : $object->name, $context);
    }
}
