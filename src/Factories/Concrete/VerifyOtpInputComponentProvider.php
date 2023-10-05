<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Common\ContextConstants;
use Apie\Core\Utils\ConverterUtils;
use Apie\HtmlBuilders\Components\Forms\VerifyOtpInput;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\OtpValueObjects\VerifyOTP;
use ReflectionProperty;
use ReflectionType;

class VerifyOtpInputComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        if (!$context->getApieContext()->hasContext(ContextConstants::RESOURCE)) {
            return false;
        }
        $class = ConverterUtils::toReflectionClass($type);
        do {
            if ($class->name === VerifyOTP::class) {
                return true;
            }
            $class = $class->getParentClass();
        } while ($class);
        return false;
    }

    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $value = $context->getFilledInValue($type->allowsNull() ? null : false);
        $class = ConverterUtils::toReflectionClass($type);
        /** @var ReflectionProperty $property */
        $property = $class->getMethod('getOtpReference')->invoke(null);
        /** @var string $label */
        $label = $class->getMethod('getOtpLabel')->invoke(null);
        $resource = $context->getApieContext()->getContext(ContextConstants::RESOURCE);
        $otpSecret = $property->getValue($resource);

        return new VerifyOtpInput(
            $context->getFormName(),
            $value,
            $label,
            $otpSecret,
            $type->allowsNull(),
            $context->getValidationError(),
        );
    }
}
