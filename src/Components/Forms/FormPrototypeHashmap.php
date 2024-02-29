<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormPrototypeHashmap extends BaseComponent
{
    /**
     * @param null|array<string|int, mixed> $value
     * @param array<string, string> $validationErrors
     */
    public function __construct(
        FormName $name,
        ?array $value,
        string $prototypeName,
        ComponentInterface $prototype,
        array $validationErrors = [],
    ) {
        $value ??= [];
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'prototypeName' => $prototypeName,
                'validationErrors' => $validationErrors,
            ],
            new ComponentHashmap([
                '__proto__' => $prototype,
            ])
        );
    }
}
