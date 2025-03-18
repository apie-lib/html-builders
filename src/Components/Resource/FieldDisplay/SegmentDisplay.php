<?php
namespace Apie\HtmlBuilders\Components\Resource\FieldDisplay;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class SegmentDisplay extends BaseComponent
{
    /**
     * @param array<string|int, ComponentInterface> $detailComponents
     */
    public function __construct(array $detailComponents, bool $showKeys = true)
    {
        parent::__construct(
            [
                'componentNames' => array_keys($detailComponents),
                'showKeys' => $showKeys,
            ],
            new ComponentHashmap($detailComponents)
        );
    }
}
