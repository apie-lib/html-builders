<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Core\Context\ApieContext;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\DiscriminatorColumn;
use Apie\Core\Metadata\Fields\SetterMethod;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Core\ReflectionTypeFactory;
use Apie\HtmlBuilders\Components\Forms\FormGroup;
use Apie\HtmlBuilders\Components\Forms\FormPrototypeList;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\Factories\Concrete\BooleanComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\DateTimeComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\EntityComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\EnumComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\FloatComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\HiddenIdComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\IntComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\ItemHashmapComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\ItemListComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\PasswordComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\PolymorphicEntityComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\UnionTypehintComponentProvider;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionParameter;
use ReflectionType;

final class FormComponentFactory
{
    /** @var FormComponentProviderInterface[] */
    private $formComponentProviders;

    public function __construct(FormComponentProviderInterface... $formComponentProviders)
    {
        $this->formComponentProviders = $formComponentProviders;
    }

        public static function create(FormComponentProviderInterface... $formComponentProviders): FormComponentFactory
        {
            return new self(
                new PasswordComponentProvider(),
                new HiddenIdComponentProvider(),
                new UnionTypehintComponentProvider(),
                new PolymorphicEntityComponentProvider(),
                new ItemListComponentProvider(),
                new ItemHashmapComponentProvider(),
                new BooleanComponentProvider(),
                new EnumComponentProvider(),
                new FloatComponentProvider(),
                new IntComponentProvider(),
                new DateTimeComponentProvider(),
                new EntityComponentProvider(),
                ...$formComponentProviders,
            );
        }

        /**
         * @param array<string|int, mixed> $filledIn
         */
        public function createFormBuildContext(ApieContext $context, array $filledIn = []): FormBuildContext
        {
            return new FormBuildContext(
                $this,
                $context->withContext(FormComponentFactory::class, $this),
                $filledIn
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

            return new Input(
                $context->getFormName(),
                $context->getFilledInValue($allowsNull ? null : ''),
                'text',
                [],
                $allowsNull,
                $context->getValidationError()
            );
        }

        public function createFromParameter(ReflectionParameter $parameter, FormBuildContext $context): ComponentInterface
        {
            $childContext = $context->createChildContext($parameter->name);
            $typehint = $parameter->getType();
            $component = $this->createFromType($typehint, $childContext);
            if ($parameter->isVariadic()) {
                return new FormPrototypeList(
                    $childContext->getFormName(),
                    $childContext->getFilledInValue([]),
                    $component
                );
            }
            return $component;
        }

        /**
         * @param ReflectionClass<object> $class
         */
        public function createFromClass(ReflectionClass $class, FormBuildContext $context, bool $providerCheck = true): ComponentInterface
        {
            if ($providerCheck) {
                $typehint = ReflectionTypeFactory::createReflectionType($class->name);
                foreach ($this->formComponentProviders as $formComponentProvider) {
                    if ($formComponentProvider->supports($typehint, $context)) {
                        return $formComponentProvider->createComponentFor($typehint, $context);
                    }
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
                                $components[] = $this->createFromParameter($parameter, $context);
                                break;
                            }
                        }
                        break;
                    case DiscriminatorColumn::class:
                        break;
                    default:
                        $components[] = $this->createFromType($reflectionData->getTypehint(), $childContext);
                }
            }
            return new FormGroup(
                $context->getFormName(),
                $context->getValidationError(),
                ...$components
            );
        }
}
