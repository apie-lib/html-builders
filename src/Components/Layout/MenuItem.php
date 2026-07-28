<?php
namespace Apie\HtmlBuilders\Components\Layout;

use Apie\Common\MenuStructure\MenuNode;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class MenuItem extends BaseComponent
{
    /**
     * @param array<int, string> $currentPath
     */
    public function __construct(
        MenuNode $menuItem,
        array $currentPath = [],
    ) {
        $components = [];
        foreach ($menuItem->children as $path => $child) {
            $components[] = new MenuItem($child, [...$currentPath, $path]);
        }
        parent::__construct(
            [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'route' => $menuItem->route,
                'icon' => $menuItem->icon,
                'subcomponents' => array_keys($components),
            ],
            new ComponentHashmap($components)
        );
    }
}
