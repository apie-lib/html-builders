<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ActionList;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class ResourceActionList extends BaseComponent
{
    public function __construct(
        CurrentConfiguration $currentConfiguration,
        ActionList $resourceActionList,
        ComponentInterface $filterColumns,
    ) {
        parent::__construct(
            [
                'actions' => $resourceActionList,
                'config' => $currentConfiguration,
            ],
            new ComponentHashmap([
                'filterColumns' => $filterColumns,
            ])
        );
    }
}
