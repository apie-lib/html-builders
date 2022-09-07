<?php
namespace Apie\HtmlBuilders\Interfaces;

interface ComponentInterface
{
    public function getComponent(string $key): ComponentInterface;

    public function getAttribute(string $key): mixed;
}
