<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Core\Entities\EntityInterface;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use ReflectionClass;

interface SingleResourceActionInterface
{
    /**
     * @template T of EntityInterface
     * @param T $entity
     * @param ReflectionClass<T> $entityClass
     */
    public static function createForEntity(EntityInterface $entity, ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self;

    public function getName(): string;

    public function getUrl(CurrentConfiguration $currentConfiguration): string;
}
