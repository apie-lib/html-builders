<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\Core\Datalayers\Lists\PaginatedResult;
use Apie\HtmlBuilders\Components\BaseComponent;

class Pagination extends BaseComponent
{
    public function __construct(PaginatedResult $paginatedResult)
    {
        parent::__construct([
            'totalCount' => $paginatedResult->totalCount,
            'pageNumber' => $paginatedResult->pageNumber,
            'pageSize' => $paginatedResult->pageSize,
            'shownCount' => $paginatedResult->list->count(),
            'querySearch' => $paginatedResult->querySearch,
        ]);
    }
}
