<?php

declare(strict_types=1);

namespace App\Interpolation;

use App\Interpolation\FeeCalculator\AbstractFeeCalculator;
use App\Interpolation\FeeCalculator\Exception\MissingFeeCalculatorException;
use App\Interpolation\Model\Application;

final class FeeCalculatorManager
{
    /**
     * @param AbstractFeeCalculator[] $calculators
     */
    public function __construct(
        private array $calculators
    ) {
    }

    public function calculate(Application $application): void
    {
        foreach ($this->calculators as $calculator) {
            $calculator->setApplication($application);

            if (true === $calculator->isSupported()) {
                $calculator->validate();
                $calculator->calculate();

                return;
            }
        }

        throw new MissingFeeCalculatorException();
    }
}
