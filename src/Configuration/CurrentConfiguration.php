<?php
namespace Apie\HtmlBuilders\Configuration;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;

final class CurrentConfiguration
{
    private ApieContext $apieContext;

    private ?BoundedContextId $selected;

    public function __construct(
        private readonly array $config,
        ApieContext $apieContext,
        private BoundedContextHashmap $boundedContextHashmap,
        ?BoundedContextId $selected = null
    ) {
        $this->apieContext = $apieContext->withContext(self::class, $this);
        $this->selected = $selected;
    }

    public function getApieContext(): ApieContext
    {
        return $this->apieContext;
    }

    public function getBoundedContextHashmap(): BoundedContextHashmap
    {
        return $this->boundedContextHashmap;
    }

    public function getSelectedBoundedContextId(): ?BoundedContextId
    {
        return $this->selected;
    }

    public function getSelectedBoundedContext(): ?BoundedContext
    {
        if ($this->selected === null) {
            return null;
        }
        return $this->boundedContextHashmap[$this->selected->toNative()] ?? null;
    }

    public function getGlobalUrl(string $path): string
    {
        $url = rtrim($this->config['base_url'] ?? '/');
        return $url . '/' . ltrim($path, '/');
    }

    public function getContextUrl(string $path): string
    {
        $id = $this->getSelectedBoundedContextId();
        return $this->getGlobalUrl('/' . $id . '/' . ltrim($path, '/'));
    }

    public function getBrowserTitle(string $pageTitle): string
    {
        return sprintf((string) ($this->config['head']['title-format'] ?? 'Apie CMS - %s'), $pageTitle);
    }

    public function shouldDisplayBoundedContextSelect(): bool
    {
        return filter_var($this->config['application']['bounded-context-select'] ?? true, FILTER_VALIDATE_BOOL);
    }
}
