<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;

class HtmlField extends BaseComponent
{
    public function __construct(
        string $name,
        ?string $value,
        bool $nullable = false,
        ?string $validationError = null
    ) {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'nullable' => $nullable,
                'validationError' => $validationError,
            ]
        );
    }
}
