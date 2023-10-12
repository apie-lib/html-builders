<?php
namespace Apie\HtmlBuilders\Lists;

use Apie\Core\Lists\ItemList;
use Apie\HtmlBuilders\ResourceActions\ResourceActionInterface;

class ActionList extends ItemList
{
    public function offsetGet(mixed $offset): ResourceActionInterface
    {
        return parent::offsetGet($offset);
    }
}
