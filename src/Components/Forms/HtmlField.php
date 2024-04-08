<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\CommonValueObjects\SafeHtml;
use Apie\Core\ValueObjects\Interfaces\StringValueObjectInterface;
use Apie\HtmlBuilders\Components\BaseComponent;

class HtmlField extends BaseComponent
{
    /**
     * @param class-string<StringValueObjectInterface> $valueObjectClass
     */
    public function __construct(
        string $name,
        ?string $value,
        bool $nullable = false,
        ?string $validationError = null,
        string $valueObjectClass = SafeHtml::class,
    ) {
        $value = $valueObjectClass::fromNative($value)->toNative();
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'nullable' => $nullable,
                'validationError' => $validationError,
            ]
        );
    }
}
