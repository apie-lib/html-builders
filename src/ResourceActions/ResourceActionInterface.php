<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Core\Entities\EntityInterface;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use ReflectionClass;

interface ResourceActionInterface
{
    /**
     * @param ReflectionClass<EntityInterface> $entityClass
     */
    public static function createFor(ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self;

    public function getName(): string;

    public function getUrl(CurrentConfiguration $currentConfiguration): string;
}
