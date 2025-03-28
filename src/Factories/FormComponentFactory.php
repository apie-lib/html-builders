<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Core\Attributes\AllowMultipart;
use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Dto\CmsInputOption;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\DiscriminatorColumn;
use Apie\Core\Metadata\Fields\SetterMethod;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Components\Forms\FormGroup;
use Apie\HtmlBuilders\Components\Forms\FormPrototypeList;
use Apie\HtmlBuilders\Components\Forms\SingleInput;
use Apie\HtmlBuilders\Factories\Concrete\ApieSingleInputComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\ArrayComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\BooleanComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\DateTimeComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\EntityComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\FileUploadComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\FloatComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\HiddenIdComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\HideUuidAsIdComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\IntComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\ItemHashmapComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\ItemListComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\MixedComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\MultiSelectComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\NullComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\OptionsComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\PolymorphicEntityComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\UnionTypehintComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\VerifyOtpInputComponentProvider;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionClass;
use ReflectionParameter;
use ReflectionType;
use SensitiveParameter;
use Throwable;

final class FormComponentFactory
{
    /** @var FormComponentProviderInterface[] */
    private $formComponentProviders;

    public function __construct(FormComponentProviderInterface... $formComponentProviders)
    {
        $this->formComponentProviders = $formComponentProviders;
    }

    /**
     * @param FormComponentProviderInterface[] $formComponentProviders
     */
    public static function create(iterable $formComponentProviders = []): FormComponentFactory
    {
        return new self(
            ...[
                ...$formComponentProviders,
                new VerifyOtpInputComponentProvider(),
                new ApieSingleInputComponentProvider(),
                new FileUploadComponentProvider(),
                new HideUuidAsIdComponentProvider(),
                new HiddenIdComponentProvider(),
                new MixedComponentProvider(),
                new MultiSelectComponentProvider(),
                new OptionsComponentProvider(),
                new UnionTypehintComponentProvider(),
                new PolymorphicEntityComponentProvider(),
                new ArrayComponentProvider(),
                new ItemListComponentProvider(),
                new ItemHashmapComponentProvider(),
                new NullComponentProvider(),
                new BooleanComponentProvider(),
                new FloatComponentProvider(),
                new IntComponentProvider(),
                new DateTimeComponentProvider(),
                new EntityComponentProvider(),
            ]
        );
    }

    /**
     * @param array<string|int, mixed> $filledIn
     */
    public function createFormBuildContext(ApieContext $context, array $filledIn = []): FormBuildContext
    {
        $multipart = false;
        $resourceName = $context->getContext(ContextConstants::RESOURCE_NAME, false);
        if ($resourceName && class_exists($resourceName)) {
            $refl = new ReflectionClass($resourceName);
            $multipart = !empty($refl->getAttributes(AllowMultipart::class));
        }
        return new FormBuildContext(
            $this,
            $context->withContext(FormComponentFactory::class, $this),
            $filledIn,
            $multipart
        );
    }

    public function createFromType(?ReflectionType $typehint, FormBuildContext $context): ComponentInterface
    {
        foreach ($this->formComponentProviders as $formComponentProvider) {
            if ($formComponentProvider->supports($typehint, $context)) {
                return $formComponentProvider->createComponentFor($typehint, $context);
            }
        }
        $metadata = MetadataFactory::getCreationMetadata(
            $typehint ?? ReflectionTypeFactory::createReflectionType('mixed'),
            $context->getApieContext()
        );
        if ($metadata instanceof CompositeMetadata) {
            return $this->createFromMetadata($metadata, $context);
        }
        $allowsNull = $typehint === null || $typehint->allowsNull();

        $value = null;
        try {
            $value = Utils::toString($context->getFilledInValue($allowsNull ? null : '', true));
        } catch (Throwable) {
        }
        return new SingleInput(
            $context->getFormName(),
            $context->getFilledInValue(),
            $context->createTranslationLabel(),
            $allowsNull,
            $typehint,
            new CmsSingleInput($context->isSensitive() ? ['password'] : ['text']),
        );
    }

    public function createFromParameter(ReflectionParameter $parameter, FormBuildContext $context): ComponentInterface
    {
        $sensitive = $context->isSensitive();
        foreach ($parameter->getAttributes(SensitiveParameter::class) as $param) {
            $sensitive = true;
        }
        $childContext = $context->createChildContext($parameter->name, $sensitive);
        $typehint = $parameter->getType();
        if ($parameter->isVariadic()) {
            $prototypeName = $context->getFormName()->getPrototypeName();
            $component = $this->createFromType($typehint, $context->createChildContext($prototypeName));
            return new FormPrototypeList(
                $childContext->getFormName(),
                $childContext->getFilledInValue([]),
                $prototypeName,
                $component
            );
        } else {
            $component = $this->createFromType($typehint, $childContext);
        }
        return $component;
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function createFromClass(ReflectionClass $class, FormBuildContext $context, ?FormComponentProviderInterface $skipProvider = null): ComponentInterface
    {
        $typehint = ReflectionTypeFactory::createReflectionType($class->name);
        foreach ($this->formComponentProviders as $formComponentProvider) {
            if ($skipProvider !== $formComponentProvider && $formComponentProvider->supports($typehint, $context)) {
                return $formComponentProvider->createComponentFor($typehint, $context);
            }
        }

        $metadata = MetadataFactory::getCreationMetadata($class, $context->getApieContext());
        return $this->createFromMetadata($metadata, $context);
    }

    public function createFromMetadata(MetadataInterface $metadata, FormBuildContext $context): ComponentInterface
    {
        if (!$metadata instanceof CompositeMetadata) {
            throw new InvalidTypeException($metadata, CompositeMetadata::class);
        }

        $components = [];
        foreach ($metadata->getHashmap() as $fieldName => $reflectionData) {
            if (!$reflectionData->isField()) {
                continue;
            }
            $childContext = $context->createChildContext($fieldName);
            switch (get_class($reflectionData)) {
                case SetterMethod::class:
                    foreach ($reflectionData->getMethod()->getParameters() as $parameter) {
                        if ($parameter->name === $fieldName) {
                            $components[$fieldName] = $this->createFromParameter($parameter, $context);
                            break;
                        }
                    }
                    break;
                case DiscriminatorColumn::class:
                    $components[$fieldName] = new SingleInput(
                        $childContext->getFormName(),
                        $childContext->getFilledInValue(),
                        $childContext->createTranslationLabel(),
                        false,
                        ReflectionTypeFactory::createReflectionType('string'),
                        new CmsSingleInput(
                            ['forced_hidden', 'hidden'],
                            new CmsInputOption(forcedValue: $reflectionData->getValueForClass($metadata->toClass()))
                        ),
                    );
                    break;
                default:
                    $components[$fieldName] = $this->createFromType($reflectionData->getTypehint(), $childContext);
            }
        }
        return new FormGroup(
            $context->getFormName(),
            $context->getValidationError(),
            $context->getMissingValidationErrors($components),
            ...array_values($components)
        );
    }
}
