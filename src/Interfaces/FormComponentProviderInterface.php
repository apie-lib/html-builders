<?php
namespace Apie\HtmlBuilders\Interfaces;

use Apie\HtmlBuilders\FormBuildContext;
use ReflectionType;

interface FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool;
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface;
}
