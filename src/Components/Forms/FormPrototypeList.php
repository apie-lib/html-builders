<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormPrototypeList extends BaseComponent
{
    public function __construct(FormName $name, ?array $value, ComponentInterface $prototype)
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
            ],
            new ComponentHashmap([
                '__proto__' => $prototype->withName($name->getPrototypeName()),
            ])
        );
    }
}
