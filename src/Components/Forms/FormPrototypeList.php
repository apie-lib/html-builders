<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormPrototypeList extends BaseComponent
{
    /** @param array<string|int, mixed>|null $value */
    public function __construct(FormName $name, ?array $value, string $prototypeName, ComponentInterface $prototype)
    {
        $rendered = [];
        /*foreach ($value ?? [] as $key => $value) {
            $rendered[$key] = $prototype->withName($name->createChildForm($key), $value);
        }*/
        parent::__construct(
            [
                'name' => $name,
                'alreadyRendered' => array_keys($rendered),
                'value' => $value,
                'prototypeName' => $prototypeName,
            ],
            new ComponentHashmap([
                '__proto__' => $prototype,
                ...$rendered
            ])
        );
    }
}
