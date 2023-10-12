<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Core\Entities\EntityInterface;
use ReflectionClass;

interface ResourceActionInterface
{
    /**
     * @param ReflectionClass<EntityInterface> $entityClass
     */
    public static function createFor(ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self;

    public function getName(): string;
}
