<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Components\Layout;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
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

    public function createWrapLayout(string $pageTitle, ?BoundedContextId $boundedContextId, ApieContext $context, ComponentInterface $contents): ComponentInterface
    {
        $configuration = $this->applicationConfiguration->createConfiguration($context, $this->boundedContextHashmap, $boundedContextId);
        return new Layout(
            $pageTitle,
            $configuration,
            $contents
        );
    }
}
