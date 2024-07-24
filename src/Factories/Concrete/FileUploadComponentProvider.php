<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Utils\ConverterUtils;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionType;

final class FileUploadComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        $class = ConverterUtils::toReflectionClass($type);
        return $class && ($class->name === UploadedFileInterface::class || in_array(UploadedFileInterface::class, $class->getInterfaceNames()));
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        return new Input(
            $context->getFormName(),
            null,
            'file',
            nullable: $type->allowsNull(),
            validationError: $context->getValidationError()
        );

    }
}