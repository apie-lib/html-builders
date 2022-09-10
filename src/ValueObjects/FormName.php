<?php
namespace Apie\HtmlBuilders\ValueObjects;

use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\HtmlBuilders\Exceptions\EmptyFormNameException;
use Stringable;

final class FormName implements ValueObjectInterface, Stringable
{
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

    public function getPrototypeName(): string
    {
        return strtoupper(str_replace(
            ['[', ']'],
            '__',
            '__' . $this . '__'
        )) . md5((string) $this);
    }

    public function getTypehintName(): string
    {
        return '_apie[typehint][' . implode('][', $this->internal) . ']';
    }

    public function __toString(): string
    {
        return 'form[' . implode('][', $this->internal) . ']';
    }
}
