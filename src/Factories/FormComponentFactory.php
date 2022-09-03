<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\Factories\Concrete\BooleanComponentProvider;
use Apie\HtmlBuilders\Factories\Concrete\EnumComponentProvider;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Utils;
use ReflectionParameter;

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
                new BooleanComponentProvider(),
                new EnumComponentProvider(),
                ...$formComponentProviders,
            );
        }

        public function createFromParameter(ApieContext $context, ReflectionParameter $parameter, array $prefix, array $filledIn): ComponentInterface
        {
            $prefix =  [...$prefix, $parameter->name];
            $context = $context->withContext(FormComponentFactory::class, $this);
            $typehint = $parameter->getType();
            foreach ($this->formComponentProviders as $formComponentProvider) {
                if ($formComponentProvider->supports($typehint, $context)) {
                    return $formComponentProvider->createComponentFor($typehint, $context, $prefix, $filledIn);
                }
            }

            return new Input(
                Utils::toFormName($prefix),
                $filledIn[$parameter->name] ?? ''
            );
        }
}
