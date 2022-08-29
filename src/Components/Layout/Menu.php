<?php

namespace Apie\HtmlBuilders\Components\Layout;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;

class Menu extends BaseComponent
{
    public function __construct(CurrentConfiguration $currentConfiguration)
    {
        $menuItems = [];
        $boundedContext = $currentConfiguration->getSelectedBoundedContext();
        if ($boundedContext) {
            foreach ($boundedContext->resources as $resource) {
                $menuItems[] = [
                    'url' => $currentConfiguration->getContextUrl('resource/' . $resource->getShortName()),
                    'title' => $resource->getShortName(),
                ];
            }
        }
        parent::__construct([
            'menuItems' => $menuItems
        ]);
    }
}