<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use LogicException;

class Input extends BaseComponent
{
    /**
     * @param array<string, string|int> $additionalAttributes
     */
    public function __construct(string $name, string $value, string $type = 'text', array $additionalAttributes = [])
    {
        if ($type === 'hidden') {
            throw new LogicException(
                'Do not use class ' . __CLASS__ . ' for hidden input fields, use ' . HiddenField::class . ' instead.'
            );
        }
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
