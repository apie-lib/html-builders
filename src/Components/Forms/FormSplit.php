<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormSplit extends BaseComponent
{
    public function __construct(FormName $name, string $value, ComponentHashmap $tabComponents)
    {
        parent::__construct(
            [
                'name' => $name,
                'tabs' => array_keys($tabComponents->toArray()),
                'value' => $value,
            ],
            $tabComponents
        );
    }

    public function withName(FormName $name): ComponentInterface
    {
        $item = clone $this;
        $item->attributes['name'] = $name;
        foreach ($item->childComponents as $key => $component) {
            $item->childComponents[$key] = $component->withName($name);
        }
        return $item;
    }
}
