<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\CmsApiDropdownOption\DropdownOptionProvider\DropdownOptionProviderInterface;
use Apie\Common\ContextConstants;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\HtmlBuilders\Components\Forms\InputWithAutocomplete;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionType;

class DropdownOptionsComponentProvider implements FormComponentProviderInterface
{
    public function __construct(
        private readonly ApplicationConfiguration $applicationConfiguration
    ) {
    }
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        $apieContext = $context->getApieContext();
        $formName = $context->getFormName();
        if (!$apieContext->hasContext(DropdownOptionProviderInterface::class)
            || !$formName->hasChildFormFieldName()) {
            return false;
        }
        $dropdownOptionProvider = $apieContext->getContext(DropdownOptionProviderInterface::class);
        assert($dropdownOptionProvider instanceof DropdownOptionProviderInterface);
        
        return $dropdownOptionProvider->supports(
            $apieContext->withContext('property', $context->getFormName()->toValidationErrorKey())
        );
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $apieContext = $context->getApieContext();
        $configuration = $this->applicationConfiguration->createConfiguration(
            $apieContext,
            $apieContext->getContext(BoundedContextHashmap::class),
            new BoundedContextId($apieContext->getContext(ContextConstants::BOUNDED_CONTEXT_ID))
        );
        $resource = new ReflectionClass($apieContext->getContext(ContextConstants::RESOURCE_NAME));
        
        return new InputWithAutocomplete(
            $context->getFormName(),
            $context->getFilledInValue(),
            $configuration->getContextUrl('/' . $resource->getShortName() . '/dropdown-options/' . $context->getFormName()->toValidationErrorKey()),
            [],
            $type->allowsNull(),
            $context->getValidationError()
        );
    }
}