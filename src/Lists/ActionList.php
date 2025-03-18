<?php
namespace Apie\HtmlBuilders\Lists;

use Apie\Core\Lists\ItemList;
use Apie\HtmlBuilders\ResourceActions\ResourceActionInterface;
use Apie\HtmlBuilders\ResourceActions\SingleResourceActionInterface;

class ActionList extends ItemList
{
    public function offsetGet(mixed $offset): ResourceActionInterface|SingleResourceActionInterface
    {
        return parent::offsetGet($offset);
    }
}
