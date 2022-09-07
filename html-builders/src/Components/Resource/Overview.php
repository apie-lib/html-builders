<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class Overview extends BaseComponent
{
    public function __construct(array $listData, array $columns, ?ComponentInterface $pagination = null)
    {
        parent::__construct(
            [
                'columns' => $columns,
                'list' => $listData,
            ],
            new ComponentHashmap([
                'pagination' => $pagination ?? new RawContents('')
            ])
        );
    }
}
