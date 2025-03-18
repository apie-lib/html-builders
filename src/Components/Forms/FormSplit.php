<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormSplit extends BaseComponent
{
    public function __construct(FormName $name, bool $isRootObject, bool $isPolymorphic, mixed $value, ComponentHashmap $tabComponents)
    {
        $newTabsComponent = [];
        $componentMap = [];
        foreach ($tabComponents as $key => $component) {
            $md5 = 's' . md5((string) $name . ',' . $key);
            $newTabsComponent[$key] = $this->makePrototype($md5, $component);
            $subName = $newTabsComponent[$key]->attributes['name'] ??
                $newTabsComponent[$key]->attributes['groupName'];
            $componentMap[(string) $subName] = $key;
        }
        
        parent::__construct(
            [
                'name' => $name,
                'isRootObject' => $isRootObject,
                'isPolymorphic' => $isPolymorphic,
                'tabs' => array_keys($newTabsComponent),
                'mapping' => $componentMap,
                'value' => $value,
            ],
            new ComponentHashmap($newTabsComponent)
        );
    }

    public function withName(FormName $name, mixed $value = null): ComponentInterface
    {
        $item = clone $this;
        $item->attributes['name'] = $name;
        foreach ($item->childComponents as $key => $component) {
            $item->childComponents[$key] = $component->withName($name, $value);
        }
        return $item;
    }
}
