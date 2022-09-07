<?php
namespace Apie\HtmlBuilders\Interfaces;

interface ComponentRendererInterface
{
    public function render(ComponentInterface $componentInterface): string;
}
