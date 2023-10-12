<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\CreateResourceActionDefinition;
use ReflectionClass;

class CreateResourceAction implements ResourceActionInterface
{
    public function __construct(private readonly CreateResourceActionDefinition $actionDefinition)
    {
    }

    public static function createFor(ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self
    {
        if ($actionDefinition instanceof CreateResourceActionDefinition) {
            return $actionDefinition->getResourceName()->name === $entityClass->name ? new self($actionDefinition) : null;
        }

        return null;
    }

    public function getName(): string
    {
        return 'Create ' . $this->actionDefinition->getResourceName()->getShortName();
    }
}
