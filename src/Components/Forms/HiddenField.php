<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;

class HiddenField extends BaseComponent
{
    public function __construct(string $name, string $value)
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value
            ]
        );
    }
}
