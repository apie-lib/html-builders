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

    public function withName(FormName $name): ComponentInterface
    {
        $item = clone $this;
        $item->attributes['name'] = $name;
        $item->childComponents = new ComponentHashmap();
        $oldComponent = $this->childComponents['__proto__'];

        $item->childComponents['__proto__'] = $oldComponent
            ->withName($name->createChildForm($name->getPrototypeName()));
        return $item;
    }
}
