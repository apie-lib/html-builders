<?php
namespace Apie\HtmlBuilders;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\ValueObjects\FormName;
use ReflectionClass;

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

    public function withApieContext(string $key, mixed $value): FormBuildContext
    {
        $res = clone $this;
        $res->context = $res->context->withContext($key, $value);
        return $res;
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
        return $this->validationErrors;
    }

    public function getFormName(): FormName
    {
        return $this->formName;
    }

    public function createTranslationLabel(): TranslationStringSet
    {
        $translations = [];
        $boundedContextId = $this->context->getContext(ContextConstants::BOUNDED_CONTEXT_ID, false);
        $resourceName = $this->context->getContext(ContextConstants::RESOURCE_NAME, false);
        $resourceName ??= $this->context->getContext(ContextConstants::METHOD_CLASS, false);
        $resourceName ??= $this->context->getContext(ContextConstants::SERVICE_CLASS, false);
        // TODO add more variations
        $translations[] = $this->formName->createTranslationString(
            new ReflectionClass($resourceName),
            $boundedContextId ? new BoundedContextId($boundedContextId) : null,
        );
        return new TranslationStringSet($translations);
    }

    public function createChildContext(string $propertyName): self
    {
        $result = clone $this;
        $result->formName = $this->formName->createChildForm($propertyName);
        $filledIn = $this->filledIn[$propertyName] ?? null;
        $result->filledIn = $filledIn;
        return $result;
    }
}
