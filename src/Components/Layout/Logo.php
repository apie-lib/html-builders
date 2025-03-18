<?php
namespace Apie\HtmlBuilders\Components\Layout;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;

class Logo extends BaseComponent
{
    public function __construct(CurrentConfiguration $currentConfiguration)
    {
        parent::__construct(
            [
                'url' => $currentConfiguration->getContextUrl('/'),
            ]
        );
    }
}
