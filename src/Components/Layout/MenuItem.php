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
        int $index = 0,
        int $counterInMenu = 0,
    ) {
        $components = [];
        $submenuCounter = 0;
        foreach ($menuItem->children as $path => $child) {
            $components[] = new MenuItem($child, [...$currentPath, $path], $index + 1, $submenuCounter);
            $submenuCounter++;
        }
        parent::__construct(
            [
                'index' => $index,
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'route' => $menuItem->route,
                'icon' => $menuItem->icon,
                'allowed' => $menuItem->allowed,
                'counterInMenu' => $counterInMenu,
                'subcomponents' => array_keys($components),
            ],
            new ComponentHashmap($components)
        );
    }
}
