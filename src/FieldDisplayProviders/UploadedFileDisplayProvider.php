<?php
namespace Apie\HtmlBuilders\FieldDisplayProviders;

use Apie\Core\Entities\EntityInterface;
use Apie\HtmlBuilders\Components\Resource\FieldDisplay\LinkDisplay;
use Apie\HtmlBuilders\FieldDisplayBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\FieldDisplayComponentProviderInterface;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedFileDisplayProvider implements FieldDisplayComponentProviderInterface
{
    public function supports(mixed $object, FieldDisplayBuildContext $context): bool
    {
        return $context->getResource() instanceof EntityInterface
            && ($object instanceof UploadedFileInterface || $object instanceof UploadedFile || is_resource($object));
    }
    public function createComponentFor(mixed $object, FieldDisplayBuildContext $context): ComponentInterface
    {
        $text = 'download';
        if ($object instanceof UploadedFileInterface) {
            $text = $object->getClientFilename();
        }
        if ($object instanceof UploadedFile) {
            $text = $object->getClientOriginalName();
        }
        $resource = $context->getResource();
        assert($resource instanceof EntityInterface);
        return new LinkDisplay($text, './' . $resource->getId()->toNative() . '/' . implode('/', $context->getVisitedNodes()));
    }
}
