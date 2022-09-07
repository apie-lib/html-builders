<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Utils;

class Checkbox extends BaseComponent
{
    public function __construct(string $name, string $value)
    {
        parent::__construct(
            [
                'name' => $name,
                'internalTypeName' => Utils::internalName($name),
                'value' => $value,
            ]
        );
    }
}
