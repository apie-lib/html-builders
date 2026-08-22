<?php
namespace Apie\HtmlBuilders\Components;

use Apie\HtmlBuilders\Components\Layout\TopBar;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class Layout extends BaseComponent
{
    public function __construct(
        string $pageTitle,
        CurrentConfiguration $currentConfiguration,
        ComponentInterface $contents,
        ComponentInterface $menu,
    ) {
        parent::__construct(
            [
                'title' => $currentConfiguration->getBrowserTitle($pageTitle),
            ],
            new ComponentHashmap([
                'top' => new TopBar($currentConfiguration),
                'menu' => $menu,
                'content' => $contents,
            ])
        );
    }
}
