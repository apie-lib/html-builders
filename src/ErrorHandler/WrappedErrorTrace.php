<?php
namespace Apie\HtmlBuilders\ErrorHandler;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\Optional;
use Apie\Core\ValueObjects\CompositeValueObject;
use Apie\Core\ValueObjects\CompositeWithOwnValidation;
use JsonSerializable;

#[Description('Represents a single line in an error stacktrace often used in development mode only')]
final class WrappedErrorTrace implements CompositeWithOwnValidation, JsonSerializable
{
    use CompositeValueObject;

    #[Optional]
    private string $file;

    #[Optional]
    private int $line;

    #[Optional]
    private string $function;

    #[Optional]
    private string $class;

    #[Optional]
    private string $type;

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @return array<string, int|string|array<int,mixed>>
     */
    public function jsonSerialize(): array
    {
        return $this->toNative();
    }

    public function __get(string $key): mixed
    {
        return $this->$key;
    }

    public function getFileContents(): ?string
    {
        if (isset($this->file) && is_readable($this->file)) {
            return file_get_contents($this->file);
        }

        return null;
    }
}
