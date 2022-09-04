<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\Factories\Concrete\BooleanComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\DateTimeComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\DtoComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\EnumComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\FloatComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\IntComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\UnionTypehintComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\ValueObjectComponentProvider;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Utils;
use ReflectionParameter;
use ReflectionType;

final class FormComponentFactory
{
    /** @var FormComponentProviderInterface */
    private $formComponentProviders;

    public function __construct(FormComponentProviderInterface... $formComponentProviders)
    {
        $this->formComponentProviders = $formComponentProviders;
    }

        public static function create(FormComponentProviderInterface... $formComponentProviders): FormComponentFactory
        {
            return new self(
                new UnionTypehintComponentProvider(),
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

        public function createFromType(ApieContext $context, ?ReflectionType $typehint, array $prefix, array $filledIn): ComponentInterface
        {
            $context = $context->withContext(FormComponentFactory::class, $this);
            foreach ($this->formComponentProviders as $formComponentProvider) {
                if ($formComponentProvider->supports($typehint, $context)) {
                    return $formComponentProvider->createComponentFor($typehint, $context, $prefix, $filledIn);
                }
            }

            return new Input(
                Utils::toFormName($prefix),
                $filledIn[end($prefix)] ?? ''
            );
        }

        public function createFromParameter(ApieContext $context, ReflectionParameter $parameter, array $prefix, array $filledIn): ComponentInterface
        {
            $prefix =  [...$prefix, $parameter->name];
            $typehint = $parameter->getType();
            return $this->createFromType($context, $typehint, $prefix, $filledIn);
        }
}
