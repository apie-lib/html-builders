<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\ValueObjects\FormName;

class Checkbox extends BaseComponent
{
    public function __construct(FormName $name, ?bool $value, bool $nullable = false)
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'nullable' => $nullable,
            ]
        );
    }
}
