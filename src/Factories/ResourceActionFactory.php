<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Common\ActionDefinitionProvider;
use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\HtmlBuilders\Lists\ActionList;
use Apie\HtmlBuilders\ResourceActions\CreateResourceAction;
use Apie\HtmlBuilders\ResourceActions\GlobalMethodResourceAction;
use Apie\HtmlBuilders\ResourceActions\RemoveResourceAction;
use Apie\HtmlBuilders\ResourceActions\RunResourceMethodResourceAction;
use ReflectionClass;

final class ResourceActionFactory
{
    private const OVERVIEW_CLASSES = [
        CreateResourceAction::class,
        GlobalMethodResourceAction::class,
        RunResourceMethodResourceAction::class,
    ];

    private const DETAIL_CLASSES = [
        RunResourceMethodResourceAction::class,
        RemoveResourceAction::class,
    ];

    public function __construct(private readonly ActionDefinitionProvider $actionDefinitionProvider)
    {
    }

    /**
     * @param ReflectionClass<EntityInterface> $class
     */
    public function createResourceActionForOverview(ReflectionClass $class, ApieContext $context): ActionList
    {
        $resourceActions = [];
        $boundedContext = $context->getContext(BoundedContext::class);
        foreach ($this->actionDefinitionProvider->provideActionDefinitions($boundedContext, $context, true) as $actionDefinition) {
            foreach (self::OVERVIEW_CLASSES as $resourceActionClass) {
                $resourceAction = $resourceActionClass::createFor($class, $actionDefinition);
                if ($resourceAction) {
                    $resourceActions[] = $resourceAction;
                }
            }
        }
        return new ActionList($resourceActions);
    }

    /**
     * @template T of EntityInterface
     *
     * @param T $entity
     * @param ReflectionClass<T> $class
     */
    public function createResourceActionForDetail(EntityInterface $entity, ReflectionClass $class, ApieContext $context): ActionList
    {
        $resourceActions = [];
        $boundedContext = $context->getContext(BoundedContext::class);
        foreach ($this->actionDefinitionProvider->provideActionDefinitions($boundedContext, $context, true) as $actionDefinition) {
            foreach (self::DETAIL_CLASSES as $resourceActionClass) {
                $resourceAction = $resourceActionClass::createForEntity($entity, $class, $actionDefinition);
                if ($resourceAction) {
                    $resourceActions[] = $resourceAction;
                }
            }
        }
        return new ActionList($resourceActions);
    }
}
