<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\Attributes\CmsValidationCheck;
use Apie\Core\Utils\ConverterUtils;
use Apie\HtmlBuilders\Components\Forms\SingleInput;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionType;

class ApieSingleInputComponentProvider implements FormComponentProviderInterface
{
    /** @var array<class-string<object>, CmsSingleInput|null> $alreadyChecked */
    private static array $alreadyChecked = [];
    /** @var array<class-string<object>, (CmsValidationCheck|array<string, mixed>)[]> $alreadyCheckedValidation */
    private static array $alreadyCheckedValidation = [];

    /**
     * @param ReflectionClass<object> $class
     */
    private function getSingleInputAttribute(ReflectionClass $class): ?CmsSingleInput
    {
        if (!isset(self::$alreadyChecked[$class->name])) {
            self::$alreadyChecked[$class->name] = null;
            foreach ($class->getAttributes(CmsSingleInput::class) as $input) {
                return self::$alreadyChecked[$class->name] = $input->newInstance();
            }
            foreach ($class->getInterfaceNames() as $interfaceName) {
                self::$alreadyChecked[$class->name] ??= $this->getSingleInputAttribute(
                    new ReflectionClass($interfaceName)
                );
            }
            foreach ($class->getTraitNames() as $traitName) {
                self::$alreadyChecked[$class->name] ??= $this->getSingleInputAttribute(
                    new ReflectionClass($traitName)
                );
            }
        }
        return self::$alreadyChecked[$class->name];
    }

    /**
     * @param ReflectionClass<object> $class
     * @param ReflectionClass<object> $mainClass
     * @return (CmsValidationCheck|array<string, mixed>)[]
     */
    private function getMultipleInputAttributes(ReflectionClass $class, ReflectionClass $mainClass): array
    {
        if (!isset(self::$alreadyCheckedValidation[$class->name])) {
            self::$alreadyCheckedValidation[$class->name] = [];
            foreach ($class->getAttributes(CmsValidationCheck::class) as $input) {
                self::$alreadyCheckedValidation[$class->name][] = $input->newInstance()->toArray($mainClass);
            }
            foreach ($class->getInterfaceNames() as $interfaceName) {
                array_push(
                    self::$alreadyCheckedValidation[$class->name],
                    ...$this->getMultipleInputAttributes(
                        new ReflectionClass($interfaceName),
                        $mainClass
                    )
                );
            }
            foreach ($class->getTraitNames() as $traitName) {
                array_push(
                    self::$alreadyCheckedValidation[$class->name],
                    ...$this->getMultipleInputAttributes(
                        new ReflectionClass($traitName),
                        $mainClass
                    )
                );
            }
        }
        return self::$alreadyCheckedValidation[$class->name];
    }

    public function supports(ReflectionType $type, FormBuildContext $context): bool
    {
        $class = ConverterUtils::toReflectionClass($type);
        if ($class === null) {
            return false;
        }
        return (bool) $this->getSingleInputAttribute($class)
            || !empty($this->getMultipleInputAttributes($class, $class));
    }
    public function createComponentFor(ReflectionType $type, FormBuildContext $context): ComponentInterface
    {
        $class = ConverterUtils::toReflectionClass($type);
        $validationChecks = $this->getMultipleInputAttributes($class, $class);
        $validationError = $context->getValidationError();
        $value = $context->getFilledInValue();
        if ($validationError !== null) {
            $validationChecks[] = CmsValidationCheck::createFromStaticValue($validationError, $value);
        }
        return new SingleInput(
            $context->getFormName(),
            $value,
            $context->createTranslationLabel(),
            $type->allowsNull(),
            $type,
            $this->getSingleInputAttribute($class) ?? new CmsSingleInput(['text']),
            array_map(function (CmsValidationCheck|array  $check) use ($class) {
                if (is_array($check)) {
                    return $check;
                }
                return $check->toArray($class);
            }, $validationChecks)
        );
    }
}
