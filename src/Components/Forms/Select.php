<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Lists\ChoiceList;
use Apie\HtmlBuilders\ValueObjects\FormName;

class Select extends BaseComponent
{
    public function __construct(FormName $name, string $value, ChoiceList $choiceList)
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
