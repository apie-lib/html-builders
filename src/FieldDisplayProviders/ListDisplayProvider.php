<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\ItemList;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Utils\ConverterUtils;
use Apie\Core\Utils\HashmapUtils;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Columns\ColumnSelector;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\ListDisplay;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\SegmentDisplay;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;
use Apie\Serializer\Serializer;
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

    /**
     * @param ReflectionClass<ItemList> $refl
     */
    private function isSimpleList(ReflectionClass $refl, ApieContext $apieContext): bool
    {
        if (!$apieContext->hasContext(Serializer::class)) {
            return false;
        }
        $display = MetadataFactory::getResultMetadata($refl, $apieContext);
        foreach ($display->getHashmap() as $fieldMetadata) {
            $typehint = $fieldMetadata->getTypehint();
            $scalar = MetadataFactory::getScalarForType($typehint, true);
            if (!in_array($scalar, ScalarType::PRIMITIVES)) {
                return false;
            }
        }
        return true;
    }

    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        assert($object instanceof ItemList);
        $apieContext = $context->getApieContext();
        $refl = new ReflectionClass($object);
        $arrayType = HashmapUtils::getArrayType($refl);
        $arrayTypeClass = ConverterUtils::toReflectionClass($arrayType);
        $scalar = MetadataFactory::getScalarForType($arrayType, true);
        if ($arrayTypeClass && !in_array($scalar, ScalarType::PRIMITIVES) && $this->isSimpleList($refl, $apieContext)) {
            $serializer = $apieContext->getContext(Serializer::class);
            assert($serializer instanceof Serializer);

            return new ListDisplay(
                Utils::toArray($serializer->normalize($object, $apieContext)),
                $this->columnSelector->getColumns($arrayTypeClass, $apieContext)
            );
        }
        /** @var array<string, mixed> $detailComponents */
        $detailComponents = [];
        $childContext = $context->createChildContext('_');
        foreach ($object as $key => $value) {
            $detailComponents[$key] = $childContext->createComponentFor($value);
        }
        return new SegmentDisplay($detailComponents, showKeys: false);
    }
}
