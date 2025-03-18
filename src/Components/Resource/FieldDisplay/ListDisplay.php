<?php
namespace Apie\HtmlBuilders\Components\Resource\FieldDisplay;

use Apie\HtmlBuilders\Components\BaseComponent;

class ListDisplay extends BaseComponent
{
    /**
     * @param array<string|int, mixed> $listData
     * @param array<int, string> $columns
     */
    public function __construct(array $listData, array $columns)
    {
        parent::__construct(
            [
                'columns' => $columns,
                'list' => $listData,
            ]
        );
    }
}
