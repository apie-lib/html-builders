<?php
namespace Apie\HtmlBuilders\Utils;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\MetadataInterface;

final class FieldUtils
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function countAmountOfFields(MetadataInterface $metadata, ApieContext $apieContext): int
    {
        $count = 1;
        if ($metadata->toScalarType() === ScalarType::STDCLASS) {
            foreach ($metadata->getHashmap() as $fieldMeta) {
                $type = $fieldMeta->getTypehint();
                if ($type) {
                    $foundMetadata = MetadataFactory::getCreationMetadata($type, $apieContext);
                    $count += self::countAmountOfFields($foundMetadata, $apieContext);
                }
            }
        }
        $arrayType = $metadata->getArrayItemType();
        if ($arrayType && $metadata->toScalarType() === ScalarType::ARRAY) {
            $count++;
            $class = $arrayType->toClass();
            if ($class) {
                $foundMetadata = MetadataFactory::getCreationMetadata($class, $apieContext);
                $count += self::countAmountOfFields($foundMetadata, $apieContext);
            }
        }
        
        return $count;
    }
}
