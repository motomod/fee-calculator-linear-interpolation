<?php

declare(strict_types=1);

namespace App\Interpolation\Storage;

interface StorageInterface
{
    public function fetch(string $resourceName): mixed;
}
