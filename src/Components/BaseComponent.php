<?php
namespace Apie\HtmlBuilders\Components;

use Apie\HtmlBuilders\FormBuildContext;
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

    public function getMissingValidationErrors(FormBuildContext $formBuildContext): array
    {
        return $formBuildContext->getMissingValidationErrors($this->childComponents->toArray());
    }

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    final public function makePrototype(string $prototypeName, BaseComponent $component): BaseComponent
    {
        $component->attributes['additionalAttributes'] ??= [];
        $component->attributes['additionalAttributes']['id'] = $prototypeName;
        $component->attributes['additionalAttributes']['prototyped'] = 'prototyped';
        return $component;
    }

    public function withName(FormName $name, mixed $value = null): ComponentInterface
    {
        $item = clone $this;
        $item->attributes['name'] = $name;
        if ($value !== null || isset($item->attributes['value'])) {
            $item->attributes['value'] = $value;
        }
        foreach ($this->childComponents as $childComponentKey => $childComponent) {
            $item->childComponents[$childComponentKey] = $childComponent->withName($name->createChildForm($childComponentKey));
        }
        return $item;
    }
}
