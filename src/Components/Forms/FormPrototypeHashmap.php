<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormPrototypeHashmap extends BaseComponent
{
    /** @param array<string|int, mixed>|null $value */
    public function __construct(FormName $name, ?array $value, string $prototypeName, ComponentInterface $prototype)
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value ?? [],
                'prototypeName' => $prototypeName,
            ],
            new ComponentHashmap([
                '__proto__' => $prototype,
            ])
        );
    }
}
