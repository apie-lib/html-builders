<?php

namespace Apie\HtmlBuilders\Assets;

use RuntimeException;

final class AssetManager
{
    private array $paths = [];

    public function withAddedPath(string... $paths): self
    {
        $returnValue = new self();
        $returnValue->paths = [...$paths, ...$this->paths];

        return $returnValue;
    }

    public function getAsset(string $filename): Asset
    {
        foreach ($this->paths as $path) {
            if (file_exists($path . '/'. $filename)) {
                return new Asset($path . '/'. $filename);
            }
        }
        throw new RuntimeException('Asset ' . $filename . ' is not found!');
    }
}
