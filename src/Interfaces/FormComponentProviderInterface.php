<?php
namespace Apie\HtmlBuilders\Interfaces;

use Apie\Core\Context\ApieContext;
use ReflectionType;

interface FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool;
    public function createComponentFor(ReflectionType $type, ApieContext $context, array $prefix, array $filledIn): ComponentInterface;
}
