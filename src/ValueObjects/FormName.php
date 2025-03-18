<?php
namespace Apie\HtmlBuilders\ValueObjects;

use Apie\Core\Attributes\SchemaMethod;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Translator\ValueObjects\TranslationString;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\HtmlBuilders\Exceptions\EmptyFormNameException;
use ReflectionClass;
use Stringable;

#[SchemaMethod('createSchema')]
final class FormName implements ValueObjectInterface, Stringable
{
    /** @var array<int, string> */
    private array $internal = [];

    public function __construct(string... $parts)
    {
        $this->internal = $parts;
    }

    /**
     * @return static
     */
    public static function fromNative(mixed $input): self
    {
        if (is_string($input) && str_starts_with($input, 'form[')) {
            // remove 'form['
            $input = preg_replace('/^form\[/', '', $input);
            // remove trailing ']'
            $input = preg_replace('/\]$/', '', $input);
            $input = str_replace('][', ',', $input);
            return new static(...explode(',', $input));
        }
        if (is_iterable($input)) {
            return new static(...$input);
        }
        throw new InvalidTypeException($input, 'string|array');
    }

    public function hasChildFormFieldName(): bool
    {
        return !empty($this->internal);
    }

    public function getChildFormFieldName(): string
    {
        if (empty($this->internal)) {
            throw new EmptyFormNameException();
        }
        return end($this->internal);
    }

    public function createChildForm(string $formFieldName): self
    {
        return new self(...[...$this->internal, $formFieldName]);
    }

    /**
     * @return array<string|int, string>
     */
    public function toNative(): array
    {
        return $this->internal;
    }

    public function toValidationErrorKey(): string
    {
        return implode('.', $this->internal);
    }

    public function getPrototypeName(): string
    {
        return '__' . end($this->internal);
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function createTranslationString(ReflectionClass $class, ?BoundedContextId $boundedContextId = null): TranslationString
    {
        $suffix = '.'
            . SnakeCaseSlug::fromClass($class)
            . '.properties.'
            . strtolower(implode('.', $this->internal));
        if ($boundedContextId === null) {
            return new TranslationString('apie.resource' . $suffix);
        }
        return new TranslationString('apie.bounded.' .  $boundedContextId . $suffix);
    }

    public function __toString(): string
    {
        if (empty($this->internal)) {
            return 'form';
        }
        return 'form[' . implode('][', $this->internal) . ']';
    }

    /**
     * @return array<string, mixed>
     */
    public static function createSchema(): array
    {
        return [
            'type' => 'array',
            'items' => [
                'type' => 'string',
            ]
        ];
    }
}
