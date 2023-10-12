<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\RunResourceMethodDefinition;
use ReflectionClass;

class RunResourceMethodResourceAction implements ResourceActionInterface
{
    public function __construct(private readonly RunResourceMethodDefinition $actionDefinition)
    {
    }

    public function getName(): string
    {
        return $this->actionDefinition->getMethod()->name;
    }

    public static function createFor(ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self
    {
        if ($actionDefinition instanceof RunResourceMethodDefinition) {
            if ($actionDefinition->getResourceName()->name !== $entityClass->name) {
                return null;
            }
            return $actionDefinition->getMethod()->isStatic() ? new self($actionDefinition) : null;
        }

        return null;
    }
}
