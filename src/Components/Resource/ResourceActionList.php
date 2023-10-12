<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Lists\ActionList;

class ResourceActionList extends BaseComponent
{
    public function __construct(
        ActionList $resourceActionList
    ) {
        parent::__construct(
            [
                'actions' => $resourceActionList,
            ]
        );
    }
}
