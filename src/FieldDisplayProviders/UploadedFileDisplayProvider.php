<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\Core\ContextConstants;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\FileStorage\StoredFile;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\LinkDisplay;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;

final class UploadedFileDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return $context->getResource() instanceof EntityInterface
            && ($object instanceof UploadedFileInterface || is_resource($object));
    }
    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        $text = null;
        if ($object instanceof UploadedFileInterface) {
            $text = $object->getClientFilename();
            if (null === $text && $object instanceof StoredFile) {
                $text = $object->getServerPath();
            }
            $size = $object->getSize();
            if ($size !== null) {
                $text .= ' (' . $size . ' bytes)';
            }
        }
        $text ??= 'download';
        $resource = $context->getResource();
        assert($resource instanceof EntityInterface);
        $resourceName = (new ReflectionClass($context->getApieContext()->getContext(ContextConstants::RESOURCE_NAME)))->getShortName();
        return new LinkDisplay($text, '../action/' . $resourceName . '/' . $resource->getId()->toNative() . '/' . implode('/', $context->getVisitedNodes()));
    }
}
