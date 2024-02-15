<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormGroup extends BaseComponent
{
    /**
     * @param array<string, string> $missingValidationErrors
     */
    public function __construct(
        FormName $name,
        ?string $validationError,
        array $missingValidationErrors,
        bool $wrapScalar,
        ComponentInterface... $formElements
    ) {
        parent::__construct(
            [
                'groupName' => $name,
                'keys' => array_keys($formElements),
                'wrapScalar' => $wrapScalar,
                'validationError' => $validationError,
                'missingValidationErrors' => $missingValidationErrors
            ],
            new ComponentHashmap($formElements)
        );
    }
}
