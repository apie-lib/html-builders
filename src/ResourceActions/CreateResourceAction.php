<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\CreateResourceActionDefinition;
use Apie\Common\ActionDefinitions\ReplaceResourceActionDefinition;
use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Metadata\MetadataFactory;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Enums\ActionDefinitionVariant;
use Apie\HtmlBuilders\Utils\FieldUtils;
use ReflectionClass;

class CreateResourceAction implements ResourceActionInterface
{
    /**
     * @param ReflectionClass<EntityInterface> $entityClass
     */
    public function __construct(
        private readonly ReflectionClass $entityClass,
        private readonly CreateResourceActionDefinition|ReplaceResourceActionDefinition $actionDefinition
    ) {
    }

    public static function createFor(ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self
    {
        if ($actionDefinition instanceof CreateResourceActionDefinition) {
            return $actionDefinition->getResourceName()->name === $entityClass->name ? new self($entityClass, $actionDefinition) : null;
        }
        if ($actionDefinition instanceof ReplaceResourceActionDefinition) {
            return $actionDefinition->getResourceName()->name === $entityClass->name ? new self($entityClass, $actionDefinition) : null;
        }

        return null;
    }

    public function getName(): string
    {
        return 'Create ' . $this->actionDefinition->getResourceName()->getShortName();
    }

    public function getUrl(CurrentConfiguration $currentConfiguration): string
    {
        return $currentConfiguration->getContextUrl(
            'resource/create/' . $this->actionDefinition->getResourceName()->getShortName()
        );
    }

    public function getVariant(): ActionDefinitionVariant
    {
        return ActionDefinitionVariant::PRIMARY;
    }

    public function isSmallPage(?ApieContext $apieContext = null): bool
    {
        $apieContext ??= new ApieContext();
        $metadata = MetadataFactory::getCreationMetadata($this->entityClass, $apieContext);
        return FieldUtils::countAmountOfFields($metadata, $apieContext) < 4;
    }
}
