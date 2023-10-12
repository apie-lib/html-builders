<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\RunGlobalMethodDefinition;
use Apie\Core\Utils\ConverterUtils;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use ReflectionClass;

class GlobalMethodResourceAction implements ResourceActionInterface
{
    public function __construct(private readonly RunGlobalMethodDefinition $actionDefinition)
    {
    }

    public function getName(): string
    {
        return $this->actionDefinition->getMethod()->name;
    }

    public static function createFor(ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self
    {
        if ($actionDefinition instanceof RunGlobalMethodDefinition) {
            $method = $actionDefinition->getMethod();
            try {
                $class = ConverterUtils::toReflectionClass($method);
                return $class?->name === $entityClass->name ? new self($actionDefinition) : null;
            } catch (CanNotConvertObjectException) {
            }
        }

        return null;
    }

    public function getUrl(CurrentConfiguration $currentConfiguration): string
    {
        $method = $this->actionDefinition->getMethod();
        return $currentConfiguration->getContextUrl(
            'resource/action/' . $method->getDeclaringClass()->name . '/' . $method->getName()
        );
    }
}
