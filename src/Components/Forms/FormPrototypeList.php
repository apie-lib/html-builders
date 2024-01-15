<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormPrototypeList extends BaseComponent
{
    private string $protoKey;

    /** @param array<string|int, mixed>|null $value */
    public function __construct(FormName $name, ?array $value, ComponentInterface $prototype)
    {
        $this->protoKey = $name->getPrototypeName();
        $prototypeComponent = $prototype->withName($name->createChildForm($name->getPrototypeName()));
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
            ],
            new ComponentHashmap([
                $this->protoKey => $prototypeComponent,
                '__PROTO__' => $prototypeComponent,
            ])
        );
    }

    public function withName(FormName $name): ComponentInterface
    {
        $item = clone $this;
        $item->attributes['name'] = $name;
        $item->childComponents = new ComponentHashmap();
        $oldComponent = $this->childComponents[$this->protoKey];
        $item->protoKey = $name->getPrototypeName();

        $item->childComponents['__PROTO__'] =
        $item->childComponents[$item->protoKey] = $oldComponent
            ->withName($name->createChildForm($name->getPrototypeName()));
        return $item;
    }
}
