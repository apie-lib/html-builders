<?php

namespace Apie\HtmlBuilders\Assets;

use Symfony\Component\Mime\MimeTypes;

final class Asset
{
    public function __construct(private string $filePath)
    {
    }

    public function getContents(): string
    {
        return file_get_contents($this->filePath);
    }

    public function getBase64Url(): string
    {
        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType($this->filePath);
        return 'data:' . $mimeType . ';base64,' . base64_encode($this->getContents());
    }
}
