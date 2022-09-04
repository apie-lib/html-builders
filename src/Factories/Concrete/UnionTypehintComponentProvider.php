<?php
namespace Apie\HtmlBuilders\Factories\Concrete;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Forms\TabSplit;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\Utils;
use ReflectionType;
use ReflectionUnionType;

class UnionTypehintComponentProvider implements FormComponentProviderInterface
{
    public function supports(ReflectionType $type, ApieContext $context): bool
    {
        return $type instanceof ReflectionUnionType && $context->hasContext(FormComponentFactory::class);
    }

    /**
     * @param ReflectionUnionType $type
     */
    public function createComponentFor(ReflectionType $type, ApieContext $context, array $prefix, array $filledIn): ComponentInterface
    {
        /** @var FormComponentFactory $formComponentFactory */
        $formComponentFactory = $context->getContext(FormComponentFactory::class);
        $components = [];
        foreach ($type->getTypes() as $subType) {
            $key = $this->getSafePanelName($subType);
            $components[$key] = $formComponentFactory->createFromType($context, $subType, $prefix, $filledIn);
        }
        return new TabSplit(Utils::toFormName($prefix), $filledIn[end($prefix)] ?? '', new ComponentHashmap($components));
    }

    public function getSafePanelName(ReflectionType $type): string
    {
        $type = (string) $type;
        $pos = strrpos($type, '\\');
        if ($pos !== false) {
            $type = substr($type, $pos + 1);
        }
        return preg_replace('/[^A-Za-z0-9]/', '_', $type);
    }
}
