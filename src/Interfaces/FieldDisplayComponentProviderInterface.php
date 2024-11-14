<?php
namespace Apie\HtmlBuilders\Interfaces;

use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(FieldDisplayComponentProviderInterface::class)]
interface FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool;
    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface;
}
