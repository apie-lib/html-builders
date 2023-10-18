<?php
namespace Apie\HtmlBuilders\Components\Resource\FieldDisplay;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class SegmentDisplay extends BaseComponent
{
    /**
     * @param array<string, ComponentInterface> $detailComponents
     */
    public function __construct(array $detailComponents)
    {
        parent::__construct(
            [
                'componentNames' => array_keys($detailComponents),
            ],
            new ComponentHashmap($detailComponents)
        );
    }
}
