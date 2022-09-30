<?php

namespace Apie\HtmlBuilders\Assets;

use RuntimeException;

final class AssetManager
{
    /** @var array<int, string> $paths */
    private array $paths;

    public function __construct(string ...$paths)
    {
        $this->paths = $paths;
    }

    /** @param array<int, string> $paths */
    public static function create(array $paths)
    {
        return new self(...$paths);
    }

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
