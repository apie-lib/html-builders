<?php
namespace Apie\HtmlBuilders\Components\Resource;

use Apie\Core\Entities\EntityInterface;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class Detail extends BaseComponent
{
    public function __construct(
        EntityInterface $entity,
        SingleResourceActionList $resourceActionList,
        ComponentInterface $table
    ) {
        parent::__construct(
            [
                'entity' => $entity,
            ],
            new ComponentHashmap([
                'resourceActionList' => $resourceActionList,
                'table' => $table
            ])
        );
    }
}
