<?php
namespace Apie\HtmlBuilders\Components\Layout;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class BoundedContextSelect extends BaseComponent
{
    public function __construct(
        CurrentConfiguration $currentConfiguration
    ) {
        $contextId = $currentConfiguration->getSelectedBoundedContextId();
        parent::__construct(
            [
                'selectedBoundedContextId' => $contextId ? $contextId->toNative() : null,
                'boundedContextHashmap' => $currentConfiguration->getBoundedContextHashmap(),
                'path' => $currentConfiguration->getContextUrl('/../'),
            ],
            new ComponentHashmap([
            ])
        );
    }
}
