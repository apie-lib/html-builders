<?php
namespace Apie\HtmlBuilders\Components\Resource\FieldDisplay;

use Apie\HtmlBuilders\Components\BaseComponent;

class LinkDisplay extends BaseComponent
{
    public function __construct(string $contents, string $link)
    {
        parent::__construct([
            'contents' => $contents,
            'link' => $link,
        ]);
    }
}
