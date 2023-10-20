<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\RemoveResourceActionDefinition;
use Apie\Core\Entities\EntityInterface;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use ReflectionClass;

class RemoveResourceAction implements SingleResourceActionInterface
{
    public function __construct(
        private readonly EntityInterface $entity,
        private readonly RemoveResourceActionDefinition $actionDefinition
    ) {
    }

    public static function createForEntity(EntityInterface $entity, ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self
    {
        if ($actionDefinition instanceof RemoveResourceActionDefinition) {
            return $actionDefinition->getResourceName()->name === $entityClass->name ? new self($entity, $actionDefinition) : null;
        }

        return null;
    }

    public function getName(): string
    {
        return 'Delete';
    }

    public function getUrl(CurrentConfiguration $currentConfiguration): string
    {
        $id = $this->entity->getId()->toNative();
        return $currentConfiguration->getContextUrl(
            'resource/delete/' . $this->actionDefinition->getResourceName()->getShortName() . '/' . $id
        );
    }
}
