<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\Attributes\CmsValidationCheck;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\ValueObjects\FormName;
use ReflectionType;

class SingleInput extends BaseComponent
{
    /**
     * @param array<CmsValidationCheck|array<string, mixed>> $validationChecks
     */
    public function __construct(
        FormName $name,
        mixed $value,
        TranslationStringSet $label,
        bool $nullable,
        ReflectionType $type,
        CmsSingleInput $input,
        array $validationChecks = [],
    ) {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'label' => $label,
                'nullable' => $nullable,
                'types' => $input->types,
                'options' => $input->options->forType($type),
                'validationChecks' => $validationChecks,
            ]
        );
    }
}
