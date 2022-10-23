<?php
namespace Apie\HtmlBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\ValueObjects\FormName;
use ReflectionClass;

final class FormBuildContext
{
    private FormName $formName;

    /**
     * @param array<string|int, mixed> $filledIn
     */
    public function __construct(
        private FormComponentFactory $formComponentFactory,
        private ApieContext $context,
        private array $filledIn
    ) {
        $this->formName = new FormName();
    }

    public function getApieContext(): ApieContext
    {
        return $this->context;
    }

    public function getComponentFactory(): FormComponentFactory
    {
        return $this->formComponentFactory;
    }

    public function getFilledInValue(mixed $defaultValue = null): mixed
    {
        return $this->filled[$this->formName->getChildFormFieldName()] ?? $defaultValue;
    }

    public function getFormName(): FormName
    {
        return $this->formName;
    }

    public function createChildContext(string $propertyName): self
    {
        $result = clone $this;
        $result->formName = $this->formName->createChildForm($propertyName);
        $filledIn = $this->filledIn[$propertyName] ?? [];
        $result->filledIn = is_array($filledIn) ? $filledIn : [];

        return $result;
    }
}
