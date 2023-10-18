<?php
namespace Apie\HtmlBuilders\Interfaces;

use Apie\HtmlBuilders\FieldDisplayBuildContext;

interface FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool;
    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface;
}
