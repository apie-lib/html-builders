<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\Core\Actions\ActionResponse;
use Apie\Core\Datalayers\Lists\PaginatedResult;
use Apie\HtmlBuilders\Components\BaseComponent;
use ReflectionClass;

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