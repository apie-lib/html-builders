<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormPrototypeList extends BaseComponent
{
    /** @param array<string|int, mixed>|null $value */
    public function __construct(FormName $name, ?array $value, ComponentInterface $prototype)
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
            ],
            new ComponentHashmap([
                '__proto__' => $prototype->withName($name->createChildForm($name->getPrototypeName())),
            ])
        );
    }

    public function withName(FormName $name): ComponentInterface
    {
        $item = clone $this;
        $item->attributes['name'] = $name;
        $item->childComponents['__proto__'] = $item->childComponents['__proto__']
            ->withName($name->createChildForm($name->getPrototypeName()));
        return $item;
    }
}
