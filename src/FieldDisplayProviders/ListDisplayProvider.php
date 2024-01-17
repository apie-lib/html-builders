<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\ItemList;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Columns\ColumnSelector;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\ListDisplay;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\SegmentDisplay;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;
use ReflectionClass;

final class ListDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function __construct(
        private readonly ColumnSelector $columnSelector
    ) {
    }

    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return $object instanceof ItemList;
    }

    private function isSimpleList(ReflectionClass $refl, ApieContext $apieContext): bool
    {
        $display = MetadataFactory::getResultMetadata($refl, $apieContext);
        foreach ($display->getHashmap() as $fieldMetadata) {
            $typehint = $fieldMetadata->getTypehint();
            $scalar = MetadataFactory::getScalarForType($typehint, $fieldMetadata->allowsNull());
            if (!in_array($scalar, ScalarType::PRIMITIVES)) {
                return false;
            }
        }
        return true;
    }

    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        assert($object instanceof ItemList);
        $refl = new ReflectionClass($object);
        if ($this->isSimpleList($refl, $context->getApieContext())) {
            return new ListDisplay(
                Utils::toArray($object),
                $this->columnSelector->getColumns($refl, $context->getApieContext())
            );
        }
        /** @var array<string, mixed> $detailComponents */
        $detailComponents = [];
        $childContext = $context->createChildContext('_');
        foreach ($object as $key => $value) {
            $detailComponents[$key] = $childContext->createComponentFor($value);
        }
        return new SegmentDisplay($detailComponents);
    }
}
