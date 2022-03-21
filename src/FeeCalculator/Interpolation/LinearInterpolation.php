<?php

declare(strict_types=1);

namespace App\Interpolation\FeeCalculator\Interpolation;

use App\Interpolation\FeeCalculator\Collection\FeeCollection;
use App\Interpolation\FeeCalculator\Interpolation\Exception\ValueOutOfRangeException;
use App\Interpolation\FeeCalculator\Interpolation\Model\Range;

class LinearInterpolation implements InterpolationInterface
{
    public function estimate(FeeCollection $feeCollection, float $value): float
    {
        $range = $this->findRange($feeCollection, $value);

        /**
         * Both fees are the same, just return one.
         */
        if ($range->getPreviousFee() === $range->getNextFee()) {
            return $range->getNextFee();
        }

        $distanceBetweenClosestMatches = $range->getNextValue() - $range->getPreviousValue();
        $distanceFromPreviousFee = abs($range->getPreviousValue() - $value);

        $plot = $distanceFromPreviousFee / $distanceBetweenClosestMatches;

        $feeDifference = $range->getNextFee() - $range->getPreviousFee();

        return $range->getPreviousFee() + ($plot * $feeDifference);
    }

    private function findRange(FeeCollection $feeCollection, float $value): Range
    {
        $previousRangeStart = 0.00;
        $previousFee = 0.00;

        /**
         * @var float $rangeStart
         * @var float $fee
         */
        foreach ($feeCollection as $rangeStart => $fee) {
            if ($rangeStart > $value) {
                return new Range(
                    previousValue: $previousRangeStart,
                    previousFee: $previousFee,
                    nextValue: $rangeStart,
                    nextFee: $fee,
                );
            }

            $previousRangeStart = $rangeStart;
            $previousFee = $fee;
        }

        throw new ValueOutOfRangeException();
    }
}
