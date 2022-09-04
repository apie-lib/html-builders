<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;

class Input extends BaseComponent
{
    /**
     * @param array<string, string|int> $additionalAttributes
     */
    public function __construct(string $name, string $value, string $type = 'text', array $additionalAttributes = [])
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'type' => $type,
                'additionalAttributes' => $additionalAttributes,
            ]
        );
    }
}
