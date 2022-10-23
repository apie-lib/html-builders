<?php
namespace Apie\HtmlBuilders\Components;

use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

abstract class BaseComponent implements ComponentInterface
{
    protected ComponentHashmap $childComponents;

    /** @param array<string|int, mixed> $attributes */
    public function __construct(protected array $attributes, ?ComponentHashmap $childComponents = null)
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

    public function withName(FormName $name): ComponentInterface
    {
        $item = clone $this;
        $item->attributes['name'] = $name;
        return $item;
    }
}
