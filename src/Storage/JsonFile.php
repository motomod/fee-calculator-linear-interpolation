<?php

declare(strict_types=1);

namespace App\Interpolation\Storage;

use App\Interpolation\Storage\Exception\UnableToLocateResourceException;

class JsonFile implements StorageInterface
{
    public function __construct(
        private string $workingDir
    ) {
    }

    /** @return array<string, float|int> */
    public function fetch(string $resourceName): array
    {
        $filePath = sprintf('%s/%s', $this->workingDir, $resourceName);

        $contents = $this->fileGetContents($filePath);

        if (false === $contents) {
            throw new UnableToLocateResourceException();
        }

        return json_decode($contents, associative: true, flags: JSON_THROW_ON_ERROR);
    }

    public function fileGetContents(string $filePath): string|false
    {
        return file_get_contents($filePath);
    }
}
