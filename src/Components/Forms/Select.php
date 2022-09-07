<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Lists\ChoiceList;

class Select extends BaseComponent
{
    public function __construct(string $name, string $value, ChoiceList $choiceList)
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'options' => $choiceList
            ]
        );
    }
}
