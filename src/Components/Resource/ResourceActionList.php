<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Lists\ActionList;

class ResourceActionList extends BaseComponent
{
    public function __construct(
        CurrentConfiguration $currentConfiguration,
        ActionList $resourceActionList,
        string $textSearch = '',
    ) {
        parent::__construct(
            [
                'actions' => $resourceActionList,
                'config' => $currentConfiguration,
                'textSearch' => $textSearch,
            ]
        );
    }
}
