<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Core\Actions\ActionResponse;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Datalayers\Lists\PaginatedResult;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Components\Layout;
use Apie\HtmlBuilders\Components\Resource\Overview;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use ReflectionClass;
use Stringable;

class ComponentFactory
{
    public function __construct(
        private readonly ApplicationConfiguration $applicationConfiguration,
        private readonly BoundedContextHashmap $boundedContextHashmap
    ) {
    }

    public function createRawContents(Stringable|string $dashboardContents): ComponentInterface
    {
        return new RawContents($dashboardContents);
    }

    public function createResourceOverview(
        ActionResponse $actionResponse,
        ReflectionClass $className,
        ?BoundedContextId $boundedContextId
    ): ComponentInterface {
        assert($actionResponse->result instanceof PaginatedResult);
        $listData = Utils::toArray($actionResponse->getResultAsNativeData());
        $columns = array_keys($actionResponse->apieContext->getApplicableGetters($className)->toArray());
        return $this->createWrapLayout(
            $className->getShortName() . ' overview',
            $boundedContextId,
            $actionResponse->apieContext,
            new Overview($listData, $columns)
        );
    }

    public function createWrapLayout(
        string $pageTitle,
        ?BoundedContextId $boundedContextId,
        ApieContext $context,
        ComponentInterface $contents
    ): ComponentInterface
    {
        $configuration = $this->applicationConfiguration->createConfiguration($context, $this->boundedContextHashmap, $boundedContextId);
        return new Layout(
            $pageTitle,
            $configuration,
            $contents
        );
    }
}
