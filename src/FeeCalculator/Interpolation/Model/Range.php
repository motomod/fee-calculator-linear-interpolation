<?php

declare(strict_types=1);

namespace App\Interpolation\FeeCalculator\Interpolation\Model;

class Range
{
    public function __construct(
        private float $previousValue,
        private float $previousFee,
        private float $nextValue,
        private float $nextFee,
    ) {
    }

    public function getPreviousValue(): float
    {
        return $this->previousValue;
    }

    public function setPreviousValue(float $previousValue): Range
    {
        $this->previousValue = $previousValue;

        return $this;
    }

    public function getPreviousFee(): float
    {
        return $this->previousFee;
    }

    public function setPreviousFee(float $previousFee): Range
    {
        $this->previousFee = $previousFee;

        return $this;
    }

    public function getNextValue(): float
    {
        return $this->nextValue;
    }

    public function setNextValue(float $nextValue): Range
    {
        $this->nextValue = $nextValue;

        return $this;
    }

    public function getNextFee(): float
    {
        return $this->nextFee;
    }

    public function setNextFee(float $nextFee): Range
    {
        $this->nextFee = $nextFee;

        return $this;
    }
}
