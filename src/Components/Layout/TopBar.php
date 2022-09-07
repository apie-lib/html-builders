<?php
namespace Apie\HtmlBuilders\Components\Layout;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class TopBar extends BaseComponent
{
    public function __construct(
        CurrentConfiguration $currentConfiguration
    ) {
        parent::__construct(
            [
            ],
            new ComponentHashmap([
                'logo' => new Logo(),
                'middle' => $currentConfiguration->shouldDisplayBoundedContextSelect()
                    ? new BoundedContextSelect($currentConfiguration)
                    : new RawContents('&nbsp;'),
                'login' => new LoginSelect($currentConfiguration),
            ])
        );
    }
}
