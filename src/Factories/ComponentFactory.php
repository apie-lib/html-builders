<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Common\ContextConstants;
use Apie\Core\Actions\ActionResponse;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Datalayers\Lists\PaginatedResult;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Columns\ColumnSelector;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Components\Forms\Form;
use Apie\HtmlBuilders\Components\Layout;
use Apie\HtmlBuilders\Components\Resource\Overview;
use Apie\HtmlBuilders\Components\Resource\Pagination;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use ReflectionClass;
use ReflectionMethod;
use Stringable;

class ComponentFactory
{
    private ColumnSelector $columnSelector;

    public function __construct(
        private readonly ApplicationConfiguration $applicationConfiguration,
        private readonly BoundedContextHashmap $boundedContextHashmap,
        private readonly FormComponentFactory $formComponentFactory,
        ?ColumnSelector $columnSelector = null
    ) {
        $this->columnSelector = $columnSelector ?? new ColumnSelector();
    }

    public function createRawContents(Stringable|string $dashboardContents): ComponentInterface
    {
        return new RawContents($dashboardContents);
    }

    /**
     * @param ReflectionClass<EntityInterface> $className
     */
    public function createResourceOverview(
        ActionResponse $actionResponse,
        ReflectionClass $className,
        ?BoundedContextId $boundedContextId
    ): ComponentInterface {
        assert($actionResponse->result instanceof PaginatedResult);
        $listData = Utils::toArray($actionResponse->getResultAsNativeData()['list']);
        $columns = $this->columnSelector->getColumns($className, $actionResponse->apieContext);
        $pagination = $this->createRawContents('');
        if ($actionResponse->result->totalCount > $actionResponse->result->pageSize) {
            $pagination = new Pagination($actionResponse->result);
        }
        return $this->createWrapLayout(
            $className->getShortName() . ' overview',
            $boundedContextId,
            $actionResponse->apieContext,
            new Overview($listData, $columns, $pagination)
        );
    }

    public function createWrapLayout(
        string $pageTitle,
        ?BoundedContextId $boundedContextId,
        ApieContext $context,
        ComponentInterface $contents
    ): ComponentInterface {
        $configuration = $this->applicationConfiguration->createConfiguration($context, $this->boundedContextHashmap, $boundedContextId);
        return new Layout(
            $pageTitle,
            $configuration,
            $contents
        );
    }

    public function createFormForMethod(
        string $pageTitle,
        ReflectionMethod $method,
        ?BoundedContextId $boundedContextId,
        ApieContext $context
    ): ComponentInterface {
        $formFields = [];
        $filledIn = $context->hasContext(ContextConstants::RAW_CONTENTS)
            ? $context->getContext(ContextConstants::RAW_CONTENTS)
            : [];
        $formBuildContext = $this->formComponentFactory->createFormBuildContext($context, $filledIn);
        foreach ($method->getParameters() as $parameter) {
            $formFields[] = $this->formComponentFactory->createFromParameter($parameter, $formBuildContext);
        }
        return $this->createWrapLayout(
            $pageTitle,
            $boundedContextId,
            $context,
            new Form($method->getNumberOfParameters() > 0 ? RequestMethod::POST : RequestMethod::GET, ...$formFields)
        );
    }

    /**
     * @param ReflectionClass<EntityInterface> $class
     */
    public function createFormForResourceCreation(
        string $pageTitle,
        ReflectionClass $class,
        ?BoundedContextId $boundedContextId,
        ApieContext $context
    ): ComponentInterface {
        $filledIn = $context->hasContext(ContextConstants::RAW_CONTENTS)
            ? $context->getContext(ContextConstants::RAW_CONTENTS)
            : [];
        $formBuildContext = $this->formComponentFactory->createFormBuildContext($context, $filledIn);
        $form = $this->formComponentFactory->createFromClass($class, $formBuildContext);
        return $this->createWrapLayout(
            $pageTitle,
            $boundedContextId,
            $context,
            new Form(RequestMethod::POST, $form)
        );
    }
}
