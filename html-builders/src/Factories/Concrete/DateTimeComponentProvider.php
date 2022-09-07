<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Utils;
use DateTimeInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

class DateTimeComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            if (class_exists($type->getName()) || interface_exists($type->getName())) {
                $refl = new ReflectionClass($type->getName());
                return $refl->implementsInterface(DateTimeInterface::class);
            }
        }
        return false;
    }
    public function createComponentFor(ReflectionType $type, ApieContext $context, array $prefix, array $filledIn): ComponentInterface
    {
        return new Input(
            Utils::toFormName($prefix),
            $filledIn[end($prefix)] ?? '',
            'datetime-local'
        );
    }
}
