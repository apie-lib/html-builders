<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormSplit extends BaseComponent
{
    public function __construct(FormName $name, mixed $value, ComponentHashmap $tabComponents)
    {
        $valuePerType = [];
        foreach ($tabComponents as $componentName => $component) {
            $valuePerType[$componentName] = $component->attributes['value'] ?? null;
        }
        parent::__construct(
            [
                'name' => $name,
                'tmpl' => 's' . md5((string) $name),
                'tabs' => array_keys($tabComponents->toArray()),
                'value' => $value,
                'valuePerType' => $valuePerType,
            ],
            $tabComponents
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
