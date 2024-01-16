<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormPrototypeHashmap extends BaseComponent
{
    /** @param array<string|int, mixed>|null $value */
    public function __construct(FormName $name, ?array $value, ComponentInterface $prototype)
    {
        $prototypeComponent = $prototype->withName($name->createChildForm($name->getPrototypeName()));
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
            ],
            new ComponentHashmap([
                '__proto__' => $prototypeComponent,
            ])
        );
    }
}
