<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\ModifyResourceActionDefinition;
use Apie\Common\ActionDefinitions\RemoveResourceActionDefinition;
use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Metadata\MetadataFactory;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Enums\ActionDefinitionVariant;
use Apie\HtmlBuilders\Utils\FieldUtils;
use ReflectionClass;

class ModifyResourceAction implements SingleResourceActionInterface
{
    public function __construct(
        private readonly EntityInterface $entity,
        private readonly ModifyResourceActionDefinition $actionDefinition
    ) {
    }

    public static function createForEntity(EntityInterface $entity, ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self
    {
        if ($actionDefinition instanceof ModifyResourceActionDefinition) {
            return $actionDefinition->getResourceName()->name === $entityClass->name ? new self($entity, $actionDefinition) : null;
        }

        return null;
    }

    public function getName(): string
    {
        return 'Edit';
    }

    public function getUrl(CurrentConfiguration $currentConfiguration): string
    {
        $id = $this->entity->getId()->toNative();
        return $currentConfiguration->getContextUrl(
            'resource/edit/' . $this->actionDefinition->getResourceName()->getShortName() . '/' . $id
        );
    }

    public function getVariant(): ActionDefinitionVariant
    {
        return ActionDefinitionVariant::PRIMARY;
    }

    public function isSmallPage(?ApieContext $apieContext = null): bool
    {
        $apieContext ??= new ApieContext();
        $metadata = MetadataFactory::getModificationMetadata(new ReflectionClass($this->entity), $apieContext);
        return FieldUtils::countAmountOfFields($metadata, $apieContext) < 4;
    }
}
