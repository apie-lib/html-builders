<?php
namespace Apie\HtmlBuilders\Configuration;

use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;

class ApplicationConfiguration
{
    private array $config;

    public function __construct(array $config = [])
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
