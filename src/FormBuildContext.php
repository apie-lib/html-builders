<?php
namespace Apie\HtmlBuilders;

use Apie\Common\ContextConstants;
use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\ValueObjects\FormName;

final class FormBuildContext
{
    private FormName $formName;

    /**
     * @var array<string|int, string> $validationErrors
     */
    private array $validationErrors;

    /**
     * @param array<string|int, mixed> $filledIn
     */
    public function __construct(
        private FormComponentFactory $formComponentFactory,
        private ApieContext $context,
        private array $filledIn
    ) {
        $this->formName = new FormName();
        $this->validationErrors = $context->hasContext(ContextConstants::VALIDATION_ERRORS)
            ? $context->getContext(ContextConstants::VALIDATION_ERRORS)
            : [];
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
        return $this->filledIn[$this->formName->getChildFormFieldName()] ?? $defaultValue;
    }

    public function getValidationError(): string|null
    {
        return $this->validationErrors[$this->formName->__toString()] ?? null;
    }

    /**
     * @return array<int|string, string>
     */
    public function getValidationErrorsInContext(): array
    {
        $prefix = $this->formName->__toString();
        $result = [];
        foreach ($this->validationErrors as $key => $message) {
            if (str_starts_with($key, $prefix)) {
                $result[substr($key, strlen($prefix))] = $message;
            }
        }

        return $result;
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
