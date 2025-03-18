<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Lists\ActionList;

class SingleResourceActionList extends BaseComponent
{
    public function __construct(
        CurrentConfiguration $currentConfiguration,
        ActionList $resourceActionList,
        mixed $id
    ) {
        parent::__construct(
            [
                'actions' => $resourceActionList,
                'config' => $currentConfiguration,
                'id' => $id,
            ]
        );
    }
}
