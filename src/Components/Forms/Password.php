<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\ValueObjects\FormName;

class Password extends BaseComponent
{
    /**
     * @param class-string<ValueObjectInterface> $passwordClassname
     */
    public function __construct(string $passwordClassname, FormName $name, ?string $value, bool $nullable = false, ?string $validationError = null)
    {
        parent::__construct(
            [
                'name' => $name,
                'intendedValue' => $value,
                'type' => 'password',
                'nullable' => $nullable,
                'validationError' => $validationError,
                'additionalAttributes' => [
                    'class' => 'unbound-password',
                    'spellcheck' => 'off',
                    'autocomplete' => 'off',
                    'maxlength' => $passwordClassname::getMaxLength(),
                    'toggle-password' => 'toggle-password',
                ],
                'minLength' => $passwordClassname::getMinLength(),
                'maxLength' => $passwordClassname::getMaxLength(),
                'specialCharacters' => $passwordClassname::getAllowedSpecialCharacters(),
                'minSpecialCharacters' => $passwordClassname::getMinSpecialCharacters(),
                'minDigits' => $passwordClassname::getMinDigits(),
                'minLowerCase' => $passwordClassname::getMinLowercase(),
                'minUpperCase' => $passwordClassname::getMinUppercase(),
            ]
        );
    }
}
