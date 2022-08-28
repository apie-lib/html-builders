<?php
namespace Apie\HtmlBuilders\Components\Dashboard;

use Apie\HtmlBuilders\Components\BaseComponent;

class RawContents extends BaseComponent
{
    public function __construct(private string $html)
    {
        parent::__construct(['html' => $html]);
    }
}
