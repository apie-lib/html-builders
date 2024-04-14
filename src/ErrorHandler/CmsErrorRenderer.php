<?php

namespace Apie\HtmlBuilders\ErrorHandler;

use Apie\Common\ContextConstants;
use Apie\Common\Interfaces\DashboardContentFactoryInterface;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CmsErrorRenderer
{
    public function __construct(
        private readonly ComponentFactory $componentFactory,
        private readonly ComponentRendererInterface $componentRenderer,
        private readonly DashboardContentFactoryInterface $dashboardContentFactory,
        private readonly string $errorTemplate,
        private readonly bool $debugMode
    ) {
    }

    /**
     * @TODO: should be PSR request and response?
     *
     * @param Request $request
     * @param Throwable $error
     * @return Response
     */
    public function createCmsResponse(Request $request, Throwable $error): Response
    {
        $contents = $this->dashboardContentFactory->create($this->errorTemplate, ['error' => $error, 'debugMode' => $this->debugMode]);
        $boundedContextId = null;
        if ($request->attributes->has(ContextConstants::BOUNDED_CONTEXT_ID)) {
            $boundedContextId = new BoundedContextId($request->attributes->get(ContextConstants::BOUNDED_CONTEXT_ID));
        }
        return new Response(
            $this->componentRenderer->render(
                $this->componentFactory->createWrapLayout(
                    'Error',
                    $boundedContextId,
                    new ApieContext(),
                    $this->componentFactory->createRawContents($contents)
                ),
                new ApieContext(),
            ),
            (new WrappedError($error))->getStatusCode()
        );
    }
}
