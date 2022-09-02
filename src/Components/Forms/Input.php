<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;

class Input extends BaseComponent
{
    public function __construct(string $name, string $value, string $type = 'text')
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'type' => $type,
            ]
        );
    }
}
