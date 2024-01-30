<?php
namespace Apie\HtmlBuilders\ResourceActions;

use Apie\Common\ActionDefinitions\ActionDefinitionInterface;
use Apie\Common\ActionDefinitions\RunGlobalMethodDefinition;
use Apie\Core\Context\ApieContext;
use Apie\Core\Utils\ConverterUtils;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Enums\ActionDefinitionVariant;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use ReflectionClass;

class GlobalMethodResourceAction implements ResourceActionInterface
{
    public function __construct(private readonly RunGlobalMethodDefinition $actionDefinition)
    {
    }

    public function getName(): string
    {
        return $this->actionDefinition->getMethod()->name;
    }

    public static function createFor(ReflectionClass $entityClass, ActionDefinitionInterface $actionDefinition): ?self
    {
        if ($actionDefinition instanceof RunGlobalMethodDefinition) {
            $method = $actionDefinition->getMethod();
            try {
                $class = ConverterUtils::toReflectionClass($method);
                if ($class?->name === $entityClass->name) {
                    return new self($actionDefinition);
                }
                $class = ConverterUtils::toReflectionClass($method->getReturnType());
                if ($class?->name === $entityClass->name) {
                    return new self($actionDefinition);
                }
            } catch (CanNotConvertObjectException) {
            }
        }

        return null;
    }

    public function getUrl(CurrentConfiguration $currentConfiguration): string
    {
        $method = $this->actionDefinition->getMethod();
        return $currentConfiguration->getContextUrl(
            'action/' . $method->getDeclaringClass()->getShortName() . '/' . $method->getName()
        );
    }

    /**
     * Used for resource action buttons. For example a remove action is 'danger'. A create resource action is 'primary'
     */
    public function getVariant(): ActionDefinitionVariant
    {
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
