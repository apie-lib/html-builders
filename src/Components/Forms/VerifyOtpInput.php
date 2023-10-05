<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\OtpValueObjects\HOTPSecret;
use Apie\OtpValueObjects\TOTPSecret;

final class VerifyOtpInput extends BaseComponent
{
    public function __construct(
        string $name,
        ?string $value,
        string $label,
        HOTPSecret|TOTPSecret $otpSecret,
        bool $nullable = false,
        ?string $validationError = null,
    ) {
        parent::__construct(
            [
                'name' => $name,
                'value' => $value,
                'label' => $label,
                'nullable' => $nullable,
                'secret' => $otpSecret,
                'validationError' => $validationError,
            ]
        );
    }
}
