<?php
namespace Apie\HtmlBuilders;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;

final class FieldDisplayBuildContext
{
    /** @var callable(mixed, FieldDisplayBuildContext): ComponentInterface */
    private $createDisplayComponentFor;

    /** @var array<int, string> */
    private array $visitedNodes = [];
    
    /**
     * @param callable(mixed, FieldDisplayBuildContext): ComponentInterface $createDisplayComponentFor
     */
    public function __construct(
        callable $createDisplayComponentFor,
        private ApieContext $context,
        private mixed $resource
    ) {
        $this->createDisplayComponentFor = $createDisplayComponentFor;
    }

    /**
     * @return array<int, string>
     */
    public function getVisitedNodes(): array
    {
        return $this->visitedNodes;
    }

    public function getResource(): mixed
    {
        return $this->resource;
    }

    public function getApieContext(): ApieContext
    {
        return $this->context;
    }

    public function createComponentFor(mixed $object): ComponentInterface
    {
        return ($this->createDisplayComponentFor)($object, $this);
    }

    public function createChildContext(string $propertyName): self
    {
        $result = clone $this;
        $result->visitedNodes[] = $propertyName;

        return $result;
    }
}
