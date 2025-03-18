<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Enums\ActionDefinitionVariant;
use ReflectionClass;

interface SingleResourceActionInterface
{
    /**
     * @template T of EntityInterface
     * @param T $entity
     * @param ReflectionClass<T> $entityClass
     */
    public static function createForEntity(EntityInterface $entity, ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self;

    /**
     * Returns name/label of resource action.
     */
    public function getName(): string;

    /**
     * Returns url of resource action (form).
     */
    public function getUrl(CurrentConfiguration $currentConfiguration): string;

    /**
     * Used for resource action buttons. For example a remove action is 'danger'. A create resource action is 'primary'
     */
    public function getVariant(): ActionDefinitionVariant;

    /**
     * Can be used by the layout to render small pages/form in a sidebar instead.
     */
    public function isSmallPage(?ApieContext $apieContext = null): bool;
}
