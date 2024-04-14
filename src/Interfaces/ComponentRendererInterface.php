<?php
namespace Apie\HtmlBuilders\Interfaces;

use Apie\Core\Context\ApieContext;

interface ComponentRendererInterface
{
    public function render(ComponentInterface $componentInterface, ApieContext $apieContext): string;
}
