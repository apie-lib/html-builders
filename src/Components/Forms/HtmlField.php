<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use LogicException;

class HtmlField extends BaseComponent
{
    /**
     * @param array<string, string|int> $additionalAttributes
     */
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
