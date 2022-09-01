<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\HtmlBuilders\Components\BaseComponent;

class Overview extends BaseComponent
{
    public function __construct(array $listData, array $columns)
    {
        parent::__construct([
            'columns' => $columns,
            'list' => $listData,
        ]);
    }
}
