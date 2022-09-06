<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\Utils;

class FormSplit extends BaseComponent
{
    public function __construct(string $name, string $value, ComponentHashmap $tabComponents)
    {
        parent::__construct(
            [
                'name' => $name,
                'internalTypeName' => Utils::internalName($name),
                'tabs' => array_keys($tabComponents->toArray()),
                'value' => $value,
            ],
            $tabComponents
        );
    }
}
