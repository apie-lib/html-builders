<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;

class FileInput extends BaseComponent
{
    /**
     * @param array<string, string|int> $value
     * @param array<string, string|int> $additionalAttributes
     */
    public function __construct(
        string $name,
        ?array $value,
        bool $multipart,
        array $additionalAttributes = [],
        bool $nullable = false,
        ?string $validationError = null
    ) {
        parent::__construct(
            [
                'id' => 'a' . md5($name),
                'name' => $name,
                'value' => $value,
                'multipart' => $multipart,
                'nullable' => $nullable,
                'additionalAttributes' => $additionalAttributes,
                'validationError' => $validationError,
            ]
        );
    }
}
