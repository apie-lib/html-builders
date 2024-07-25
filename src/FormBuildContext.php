<?php
namespace Apie\HtmlBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\ValueObjects\FormName;

final class FormBuildContext
{
    private FormName $formName;

    /**
     * @var array<string|int, string> $validationErrors
     */
    private array $validationErrors;

    /**
     * @var array<string|int, mixed>|string|null $filledIn
     */
    private array|string|null $filledIn;

    /**
     * @param array<string|int, mixed> $filledIn
     */
    public function __construct(
        private FormComponentFactory $formComponentFactory,
        private ApieContext $context,
        array $filledIn,
        private bool $multipart = false
    ) {
        $this->filledIn = $filledIn;
        $this->formName = new FormName();
        $this->validationErrors = $context->hasContext(ContextConstants::VALIDATION_ERRORS)
            ? $context->getContext(ContextConstants::VALIDATION_ERRORS)
            : [];
    }

    public function isMultipart(): bool
    {
        return $this->multipart;
    }

    public function getApieContext(): ApieContext
    {
        return $this->context;
    }

    public function getComponentFactory(): FormComponentFactory
    {
        return $this->formComponentFactory;
    }

    public function getFilledInValue(mixed $defaultValue = null, bool $toString = false): mixed
    {
        $result = $this->filledIn ?? $defaultValue;
        if ($toString) {
            if (is_array($result)) {
                return $defaultValue;
            }
            return Utils::toString($result);
        }
        return $result;
    }

    public function getValidationError(): string|null
    {
        return $this->validationErrors[$this->formName->toValidationErrorKey()] ?? null;
    }

    /**
     * @param array<string, ComponentInterface> $childComponents
     *
     * @return array<int|string, string>
     */
    public function getMissingValidationErrors(array $childComponents): array
    {
        $result = $this->getValidationErrorsInContext();
        $missingValidationErrors = [];
        foreach (array_keys($childComponents) as $propertyName) {
            $prefix = $propertyName . '.';
            $found = false;
            foreach (array_keys($result) as $key) {
                if ($key !== $propertyName || !str_starts_with($key, $prefix)) {
                    $found = true;
                }
            }
            if (!$found && isset($result[$propertyName])) {
                $missingValidationErrors[$propertyName] = $result[$propertyName];
            }
        }

        return $missingValidationErrors;
    }

    /**
     * @return array<int|string, string>
     */
    public function getValidationErrorsInContext(): array
    {
        $prefix = $this->formName->toValidationErrorKey();
        $result = [];
        $prefixLength = strlen($prefix) + 1;
        foreach ($this->validationErrors as $key => $message) {
            if (str_starts_with($key, $prefix)) {
                $result[substr($key, $prefixLength)] = $message;
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
        $filledIn = $this->filledIn[$propertyName] ?? null;
        $result->filledIn = $filledIn;
        $result->validationErrors = $this->validationErrors;

        return $result;
    }
}
