<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\HtmlBuilders\ValueObjects\FormName;

class Password extends BaseComponent
{
    /**
     * @param class-string<ValueObjectInterface> $passwordClassname
     */
    public function __construct(string $passwordClassname, FormName $name, ?string $value, bool $nullable = false)
    {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'type' => 'password',
                'nullable' => $nullable,
                'additionalAttributes' => [
                    'class' => 'unbound-password',
                    'spellcheck' => 'off',
                    'autocomplete' => 'off',
                    'maxlength' => $passwordClassname::getMaxLength(),
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
