<?php
namespace Apie\HtmlBuilders\Components;

use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

abstract class BaseComponent implements ComponentInterface
{
    private ComponentHashmap $childComponents;

    public function __construct(private array $attributes, ?ComponentHashmap $childComponents = null)
    {
        $this->childComponents = $childComponents ?? new ComponentHashmap();
    }

    public function getComponent(string $key): ComponentInterface
    {
        return $this->childComponents[$key];
    }

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }
}
