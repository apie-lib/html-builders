<?php
namespace Apie\HtmlBuilders\Factories;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\FieldDisplayProviders\FallbackDisplayProvider;
use Apie\HtmlBuilders\FieldDisplayProviders\SegmentDisplayProvider;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;

final class FieldDisplayComponentFactory
{
    /**
     * @var array<int, FieldDisplayComponentProviderInterface>
     */
    private array $valueComponentProviders;

    public function __construct(FieldDisplayComponentProviderInterface ...$valueComponentProviders)
    {
        $this->valueComponentProviders = $valueComponentProviders;
    }

    public static function create(): self
    {
        return new self(
            new SegmentDisplayProvider(),
            new FallbackDisplayProvider(),
        );
    }

    private function doCreateDisplayFor(mixed $object, FieldDisplayBuildContext $fieldDisplayBuildContext): ComponentInterface
    {
        foreach ($this->valueComponentProviders as $valueComponentProvider) {
            if ($valueComponentProvider->supports($object, $fieldDisplayBuildContext)) {
                return $valueComponentProvider->createComponentFor($object, $fieldDisplayBuildContext);
            }
        }
        return (new FallbackDisplayProvider)->createComponentFor($object, $fieldDisplayBuildContext);
    }

    public function createDisplayFor(mixed $object, ApieContext $apieContext): ComponentInterface
    {
        $context = new FieldDisplayBuildContext(
            function (mixed $object, FieldDisplayBuildContext $fieldDisplayBuildContext) {
                return $this->doCreateDisplayFor($object, $fieldDisplayBuildContext);
            },
            $apieContext,
            $object
        );
        
        return $this->doCreateDisplayFor($object, $context);
    }
}
