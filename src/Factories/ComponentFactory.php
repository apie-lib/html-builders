<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Core\Actions\ActionResponse;
use Apie\Core\Attributes\Context;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Datalayers\ApieDatalayerWithFilters;
use Apie\Core\Datalayers\Lists\PaginatedResult;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\Lists\StringList;
use Apie\Core\Session\CsrfTokenProvider;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Columns\ColumnSelector;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Components\Forms\Csrf;
use Apie\HtmlBuilders\Components\Forms\Form;
use Apie\HtmlBuilders\Components\Forms\RemoveConfirm;
use Apie\HtmlBuilders\Components\Layout;
use Apie\HtmlBuilders\Components\Resource\Detail;
use Apie\HtmlBuilders\Components\Resource\FilterColumns;
use Apie\HtmlBuilders\Components\Resource\Overview;
use Apie\HtmlBuilders\Components\Resource\Pagination;
use Apie\HtmlBuilders\Components\Resource\ResourceActionList;
use Apie\HtmlBuilders\Components\Resource\SingleResourceActionList;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;
use Apie\HtmlBuilders\Enums\LayoutEnum;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Psr\Http\Message\RequestInterface;
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
        private readonly FieldDisplayComponentFactory $fieldDisplayComponentFactory,
        private readonly ResourceActionFactory $resourceActionFactory,
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
    public function createResource(
        ActionResponse $actionResponse,
        ReflectionClass $className,
        ?BoundedContextId $boundedContextId
    ): ComponentInterface {
        assert($actionResponse->result instanceof EntityInterface);
        $id = $actionResponse->apieContext->getContext(ContextConstants::RESOURCE_ID);
        $configuration = $this->applicationConfiguration->createConfiguration(
            $actionResponse->apieContext,
            $this->boundedContextHashmap,
            $boundedContextId
        );
        $actionList = $this->resourceActionFactory->createResourceActionForDetail(
            $actionResponse->result,
            $className,
            $actionResponse->apieContext
        );
        $resourceActionList = new SingleResourceActionList(
            $configuration,
            $actionList,
            $id
        );
        return $this->createWrapLayout(
            $className->getShortName() . ' details of ' . $id,
            $boundedContextId,
            $actionResponse->apieContext,
            new Detail(
                $actionResponse->result,
                $resourceActionList,
                $this->fieldDisplayComponentFactory->createDisplayFor($actionResponse->result, $actionResponse->apieContext)
            )
        );
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
        $configuration = $this->applicationConfiguration->createConfiguration(
            $actionResponse->apieContext,
            $this->boundedContextHashmap,
            $boundedContextId
        );
        $filterColumns = $this->createFilterColumns($className, $actionResponse->apieContext);

        $actionList = new ResourceActionList(
            $configuration,
            $this->resourceActionFactory->createResourceActionForOverview($className, $actionResponse->apieContext),
            $filterColumns
        );
        return $this->createWrapLayout(
            $className->getShortName() . ' overview',
            $boundedContextId,
            $actionResponse->apieContext,
            new Overview($listData, $columns, $actionList, $pagination)
        );
    }

    /**
     * @param ReflectionClass<EntityInterface> $className
     */
    public function createFilterColumns(ReflectionClass $className, ApieContext $apieContext): ComponentInterface
    {
        $searchFilters = [];
        if ($apieContext->hasContext(RequestInterface::class)) {
            $request = $apieContext->getContext(RequestInterface::class);
            assert($request instanceof RequestInterface);
            $query = $request->getUri()->getQuery();
            parse_str($query, $searchFilters);
        }
        if ($apieContext->hasContext(ApieDatalayerWithFilters::class) && $apieContext->hasContext(ContextConstants::BOUNDED_CONTEXT_ID)) {
            $datalayer = $apieContext->getContext(ApieDatalayerWithFilters::class);
            assert($datalayer instanceof ApieDatalayerWithFilters);
            $boundedContextId = $apieContext->getContext(ContextConstants::BOUNDED_CONTEXT_ID);
            $filterColumns = $datalayer->getFilterColumns($className, new BoundedContextId($boundedContextId));
            return new FilterColumns($filterColumns, $searchFilters['search'] ?? '', Utils::toArray($searchFilters['query'] ?? []));
        }
        return new FilterColumns(new StringList(), $searchFilters['search'] ?? '', []);
    }

    public function createWrapLayout(
        string $pageTitle,
        ?BoundedContextId $boundedContextId,
        ApieContext $context,
        ComponentInterface $contents,
        LayoutEnum $layoutEnum = LayoutEnum::LAYOUT
    ): ComponentInterface {
        if ($layoutEnum === LayoutEnum::SIDEBAR) {
            return $contents;
        }
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
        ApieContext $context,
        LayoutEnum $layoutEnum = LayoutEnum::LAYOUT
    ): ComponentInterface {
        /** @var CsrfTokenProvider $csrfTokenProvider */
        $csrfTokenProvider = $context->getContext(CsrfTokenProvider::class);
        $csrfToken = $csrfTokenProvider->createToken();
        $formFields = ['_csrf' => new Csrf($csrfToken)];
        $filledIn = $context->hasContext(ContextConstants::RAW_CONTENTS)
            ? $context->getContext(ContextConstants::RAW_CONTENTS)
            : [];
        $formBuildContext = $this->formComponentFactory->createFormBuildContext($context, $filledIn);
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getAttributes(Context::class)) {
                continue;
            }
            $formFields[$parameter->name] = $this->formComponentFactory->createFromParameter($parameter, $formBuildContext);
        }
        return $this->createWrapLayout(
            $pageTitle,
            $boundedContextId,
            $context,
            new Form(
                RequestMethod::POST,
                $formBuildContext->getValidationError(),
                $formBuildContext->getMissingValidationErrors($formFields),
                $filledIn,
                ...$formFields
            ),
            $layoutEnum
        );
    }

    /**
     * @param ReflectionClass<EntityInterface> $class
     */
    public function createFormForResourceRemoval(
        string $pageTitle,
        ReflectionClass $class,
        ?BoundedContextId $boundedContextId,
        ApieContext $context,
        LayoutEnum $layoutEnum = LayoutEnum::LAYOUT
    ): ComponentInterface {
        $filledIn = $context->hasContext(ContextConstants::RAW_CONTENTS)
            ? $context->getContext(ContextConstants::RAW_CONTENTS)
            : [];
        /** @var CsrfTokenProvider $csrfTokenProvider */
        $csrfTokenProvider = $context->getContext(CsrfTokenProvider::class);
        $csrfToken = $csrfTokenProvider->createToken();

        $formBuildContext = $this->formComponentFactory->createFormBuildContext($context, $filledIn);
        return $this->createWrapLayout(
            $pageTitle,
            $boundedContextId,
            $context,
            new Form(
                RequestMethod::POST,
                $formBuildContext->getValidationError(),
                $formBuildContext->getMissingValidationErrors([]),
                [],
                new RemoveConfirm($class),
                new Csrf($csrfToken)
            ),
            $layoutEnum
        );
    }

    /**
     * @param ReflectionClass<EntityInterface> $class
     */
    public function createFormForResourceCreation(
        string $pageTitle,
        ReflectionClass $class,
        ?BoundedContextId $boundedContextId,
        ApieContext $context,
        LayoutEnum $layoutEnum = LayoutEnum::LAYOUT
    ): ComponentInterface {
        $filledIn = $context->hasContext(ContextConstants::RAW_CONTENTS)
            ? $context->getContext(ContextConstants::RAW_CONTENTS)
            : [];
        /** @var CsrfTokenProvider $csrfTokenProvider */
        $csrfTokenProvider = $context->getContext(CsrfTokenProvider::class);
        $csrfToken = $csrfTokenProvider->createToken();

        $formBuildContext = $this->formComponentFactory->createFormBuildContext($context, $filledIn);
        $form = $this->formComponentFactory->createFromClass($class, $formBuildContext);
        return $this->createWrapLayout(
            $pageTitle,
            $boundedContextId,
            $context,
            new Form(
                RequestMethod::POST,
                $formBuildContext->getValidationError(),
                $form->getMissingValidationErrors($formBuildContext),
                $filledIn,
                new Csrf($csrfToken),
                $form
            ),
            $layoutEnum
        );
    }

    /**
     * @param ReflectionClass<EntityInterface> $class
     */
    public function createFormForResourceModification(
        string $pageTitle,
        ReflectionClass $class,
        ?BoundedContextId $boundedContextId,
        ApieContext $context,
        LayoutEnum $layoutEnum = LayoutEnum::LAYOUT
    ): ComponentInterface {
        $filledIn = $context->hasContext(ContextConstants::RAW_CONTENTS)
            ? $context->getContext(ContextConstants::RAW_CONTENTS)
            : [];
        /** @var CsrfTokenProvider $csrfTokenProvider */
        $csrfTokenProvider = $context->getContext(CsrfTokenProvider::class);
        $csrfToken = $csrfTokenProvider->createToken();

        $formBuildContext = $this->formComponentFactory->createFormBuildContext($context, $filledIn);
        $form = $this->formComponentFactory->createFromClass($class, $formBuildContext);
        return $this->createWrapLayout(
            $pageTitle,
            $boundedContextId,
            $context,
            new Form(
                RequestMethod::POST,
                $formBuildContext->getValidationError(),
                $form->getMissingValidationErrors($formBuildContext),
                $filledIn,
                new Csrf($csrfToken),
                $form
            ),
            $layoutEnum
        );
    }
}
