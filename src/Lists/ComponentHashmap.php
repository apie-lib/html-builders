<?php
namespace Apie\HtmlBuilders\Lists;

use Apie\Core\Lists\ItemHashmap;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;

class ComponentHashmap extends ItemHashmap
{
    public function offsetGet(mixed $offset): ComponentInterface
    {
        return parent::offsetGet($offset);
    }
}
