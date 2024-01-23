<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\Core\Lists\StringList;
use Apie\HtmlBuilders\Components\BaseComponent;

class FilterColumns extends BaseComponent
{
    public function __construct(StringList $filters, string $textSearch, array $currentFilter)
    {
        parent::__construct([
            'textSearch' => $textSearch,
            'currentFilter' => $currentFilter,
            'filters' => $filters,
        ]);
    }
}