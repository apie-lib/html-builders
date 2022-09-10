<?php
namespace Apie\HtmlBuilders\Interfaces;

use Apie\HtmlBuilders\ValueObjects\FormName;

interface ComponentInterface
{
    public function getComponent(string $key): ComponentInterface;

    public function getAttribute(string $key): mixed;

    public function withName(FormName $name): ComponentInterface;
}
