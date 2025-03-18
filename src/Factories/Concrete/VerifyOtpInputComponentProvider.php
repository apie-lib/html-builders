<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\ContextConstants;
use Apie\Core\Dto\CmsInputOption;
use Apie\Core\Utils\ConverterUtils;
use Apie\HtmlBuilders\Components\Forms\SingleInput;
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
        if (!$class) {
            return false;
        }
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
        $value = $context->getFilledInValue($type->allowsNull() ? null : false, true);
        $class = ConverterUtils::toReflectionClass($type);
        /** @var ReflectionProperty $property */
        $property = $class->getMethod('getOtpReference')->invoke(null);
        $resource = $context->getApieContext()->getContext(ContextConstants::RESOURCE);
        /** @var string $label */
        $label = $class->getMethod('getOtpLabel')->invoke(null, $resource);
        $otpSecret = $property->getValue($resource);

        return new SingleInput(
            $context->getFormName(),
            $value,
            $context->createTranslationLabel(),
            $type->allowsNull(),
            $type,
            new CmsSingleInput(
                ['otp', 'text'],
                new CmsInputOption(imageUrl: $otpSecret->getUrl($label))
            )
        );
    }
}
