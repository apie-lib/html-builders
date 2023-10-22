<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\RunResourceMethodDefinition;
use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Enums\ActionDefinitionVariant;
use ReflectionClass;

class RunResourceMethodResourceAction implements ResourceActionInterface, SingleResourceActionInterface
{
    public function __construct(
        private readonly RunResourceMethodDefinition $actionDefinition,
        private readonly ?EntityInterface $entity = null,
    ) {
    }

    public function getName(): string
    {
        return $this->actionDefinition->getMethod()->name;
    }

    public static function createForEntity(EntityInterface $entity, ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self
    {
        if ($actionDefinition instanceof RunResourceMethodDefinition) {
            if ($actionDefinition->getResourceName()->name !== $entityClass->name) {
                return null;
            }
            return $actionDefinition->getMethod()->isStatic() ? null : new self($actionDefinition, $entity);
        }

        return null;
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

    public function getUrl(CurrentConfiguration $currentConfiguration): string
    {
        $method = $this->actionDefinition->getMethod();
        if ($this->entity) {
            $id = $this->entity->getId()->toNative();
            return $currentConfiguration->getContextUrl(
                'resource/action/' . $method->getDeclaringClass()->getShortName() . '/' . $id . '/' . $method->getName()
            );
        }
        return $currentConfiguration->getContextUrl(
            'resource/action/' . $method->getDeclaringClass()->getShortName() . '/' . $method->getName()
        );
    }

    public function getVariant(): ActionDefinitionVariant
    {
        if (str_starts_with($this->actionDefinition->getMethod()->name, 'create')) {
            return ActionDefinitionVariant::PRIMARY;
        }
        if (str_starts_with($this->actionDefinition->getMethod()->name, 'remove')) {
            return ActionDefinitionVariant::DANGER;
        }
        if ($this->actionDefinition->getMethod()->isStatic()) {
            return ActionDefinitionVariant::PLAIN;
        }
        return ActionDefinitionVariant::SECONDARY;
    }

    /**
     * Can be used by the layout to render small pages/form in a sidebar instead.
     */
    public function isSmallPage(?ApieContext $apieContext = null): bool
    {
        return false;
    }
}
