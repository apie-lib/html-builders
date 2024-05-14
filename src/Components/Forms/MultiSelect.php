<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\Core\Lists\ValueOptionList;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\ValueObjects\FormName;

class MultiSelect extends BaseComponent
{
    public function __construct(FormName $name, mixed $value, ValueOptionList $optionList)
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'options' => $optionList
            ]
        );
    }
}
