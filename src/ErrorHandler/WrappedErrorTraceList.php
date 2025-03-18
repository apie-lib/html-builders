<?php
namespace Apie\HtmlBuilders\ErrorHandler;

use Apie\Core\Lists\ItemList;

final class WrappedErrorTraceList extends ItemList
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): WrappedErrorTrace
    {
        return parent::offsetGet($offset);
    }

    /**
     * @return array<string, string>
     */
    public function getFiles(): array
    {
        $result = [];

        foreach ($this as $trace) {
            $contents = $trace->getFileContents();
            if ($contents !== null) {
                $result[$trace->file] = $contents;
            }
        }

        return $result;
    }
}
