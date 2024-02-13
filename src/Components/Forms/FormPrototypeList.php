<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormPrototypeList extends BaseComponent
{
    /**
     * @param array<string|int, mixed>|null $value
     * @param array<string, string> $validationErrors
     */
    public function __construct(
        FormName $name,
        ?array $value,
        string $prototypeName,
        ComponentInterface $prototype,
        array $validationErrors = [],
    ) {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value ?? [],
                'prototypeName' => $prototypeName,
                'validationErrors' => $validationErrors,
            ],
            new ComponentHashmap([
                '__proto__' => $prototype,
            ])
        );
    }
}
