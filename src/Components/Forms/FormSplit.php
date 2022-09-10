<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
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
}
