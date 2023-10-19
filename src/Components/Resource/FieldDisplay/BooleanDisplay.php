<?php
namespace Apie\HtmlBuilders\Components\Resource\FieldDisplay;

use Apie\HtmlBuilders\Components\BaseComponent;

class BooleanDisplay extends BaseComponent
{
    public function __construct(bool $value)
    {
        parent::__construct(
            [
                'value' => $value,
            ]
        );
    }
}
