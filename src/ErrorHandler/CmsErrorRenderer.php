<?php

namespace Apie\HtmlBuilders\ErrorHandler;

use Apie\Common\ContextConstants;
use Apie\Common\Interfaces\DashboardContentFactoryInterface;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Exceptions\HttpStatusCodeException;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CmsErrorRenderer
{
    public function __construct(
        private readonly ComponentFactory $componentFactory,
        private readonly ComponentRendererInterface $componentRenderer,
        private readonly DashboardContentFactoryInterface $dashboardContentFactory,
        private readonly string $errorTemplate
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
        $contents = $this->dashboardContentFactory->create($this->errorTemplate, ['error' => $error]);
        $boundedContextId = null;
        if ($request->attributes->has(ContextConstants::BOUNDED_CONTEXT_ID)) {
            $boundedContextId = new BoundedContextId($request->attributes->get(ContextConstants::BOUNDED_CONTEXT_ID));
        }
        $statusCode = 500;
        if ($error instanceof HttpStatusCodeException || $error instanceof HttpException) {
            $statusCode = $error->getStatusCode();
        }
        return new Response(
            $this->componentRenderer->render(
                $this->componentFactory->createWrapLayout(
                    'Error',
                    $boundedContextId,
                    new ApieContext(),
                    $this->componentFactory->createRawContents($contents)
                )
            ),
            $statusCode
        );
    }
}
