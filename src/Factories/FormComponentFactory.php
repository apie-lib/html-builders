<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Forms\FormGroup;
use Apie\HtmlBuilders\Components\Forms\FormPrototypeList;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\Factories\Concrete\BooleanComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\DateTimeComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\DtoComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\EntityComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\EnumComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\FloatComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\IntComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\ItemHashmapComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\ItemListComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\PolymorphicEntityComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\UnionTypehintComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\ValueObjectComponentProvider;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use ReflectionClass;
use ReflectionMethod;
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
                new UnionTypehintComponentProvider(),
                new PolymorphicEntityComponentProvider(),
                new ItemListComponentProvider(),
                new ItemHashmapComponentProvider(),
                new EntityComponentProvider(),
                new BooleanComponentProvider(),
                new EnumComponentProvider(),
                new FloatComponentProvider(),
                new IntComponentProvider(),
                new DateTimeComponentProvider(),
                new ValueObjectComponentProvider(),
                new DtoComponentProvider(),
                ...$formComponentProviders,
            );
        }

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
            $allowsNull = $typehint === null || $typehint->allowsNull();

            return new Input(
                $context->getFormName(),
                $context->getFilledInValue($allowsNull ? null : ''),
                'text',
                [],
                $allowsNull
            );
        }

        public function createFromParameter(ReflectionParameter $parameter, FormBuildContext $context): ComponentInterface
        {
            $childContext = $context->createChildContext($parameter->name);
            $typehint = $parameter->getType();
            $component = $this->createFromType($typehint, $childContext);
            if ($parameter->isVariadic()) {
                return new FormPrototypeList(
                    $context->getFormName(),
                    $context->getFilledInValue([]),
                    $component
                );
            }
            return $component;
        }

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

            $components = [];
            $constructor = $class->getConstructor();
            if ($constructor) {
                foreach ($constructor->getParameters() as $parameter) {
                    $components[] = $this->createFromParameter($parameter, $context);
                }
            }
            foreach ($context->getApplicableSetters($class) as $key => $setter) {
                $childContext = $context->createChildContext($key);
                if ($setter instanceof ReflectionMethod) {
                    $parameters = $setter->getParameters();
                    $parameter = end($parameters);
                    $typehint = $parameter->getType();
                    $components[] = $this->createFromType($typehint, $childContext);
                } else {
                    $components[] = $this->createFromType($setter->getType(), $childContext);
                }
            }
            return new FormGroup(
                $context->getFormName(),
                ...$components
            );
        }
}
