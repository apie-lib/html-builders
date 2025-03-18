<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormGroup extends BaseComponent
{
    /**
     * @param array<string, string> $validationErrors
     */
    public function __construct(
        FormName $name,
        ?string $validationError,
        array $validationErrors,
        ComponentInterface... $formElements
    ) {
        $names = [];
        foreach ($formElements as $formElement) {
            $names[] = $this->sanitizeName($formElement->getAttribute('name'));
        }
        parent::__construct(
            [
                'groupName' => $name,
                'names' => $names,
                'keys' => array_keys($formElements),
                'validationError' => $validationError,
                'validationErrors' => $validationErrors,
            ],
            new ComponentHashmap($formElements)
        );
    }

    private function sanitizeName(mixed $name): ?string
    {
        if ($name === null) {
            return null;
        }
        return Utils::toString($name);
    }
}
