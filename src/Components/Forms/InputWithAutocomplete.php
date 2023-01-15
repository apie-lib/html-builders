<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\ValueObjects\FormName;

class InputWithAutocomplete extends BaseComponent
{
    /**
     * @param array<string, string|int> $additionalAttributes
     */
    public function __construct(
        FormName $name,
        ?string $value,
        string $ajaxCallUrl,
        array $additionalAttributes = [],
        bool $nullable = false,
        ?string $validationError = null
    ) {
        $class = 'unhandled-input-with-autocomplete';
        if (isset($additionalAttributes['class'])) {
            $additionalAttributes['class'] .= ' '. $class;
        } else {
            $additionalAttributes['class'] = $class;
        }
        $additionalAttributes['data-ajaxUrl'] = $ajaxCallUrl;
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'type' => 'text',
                'nullable' => $nullable,
                'additionalAttributes' => $additionalAttributes,
                'validationError' => $validationError,
            ]
        );
    }
}
