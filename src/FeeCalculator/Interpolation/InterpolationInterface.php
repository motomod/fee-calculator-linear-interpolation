<?php

declare(strict_types=1);

namespace App\Interpolation\FeeCalculator\Interpolation;

use App\Interpolation\FeeCalculator\Collection\FeeCollection;

interface InterpolationInterface
{
    public function estimate(FeeCollection $feeCollection, float $value): float;
}
