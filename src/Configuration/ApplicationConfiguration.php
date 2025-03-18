<?php
namespace Apie\HtmlBuilders\Configuration;

use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;

class ApplicationConfiguration
{
    /**
     * @param array<int|string, mixed> $config
     */
    public function __construct(private array $config = [])
    {
        $this->config = $config;
    }

    public function createConfiguration(
        ApieContext $context,
        BoundedContextHashmap $boundedContextHashmap,
        ?BoundedContextId $selected
    ): CurrentConfiguration {
        return new CurrentConfiguration($this->config, $context, $boundedContextHashmap, $selected);
    }
}
