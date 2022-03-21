<?php

declare(strict_types=1);

namespace App\Interpolation\FeeCalculator\Loan;

use App\Interpolation\FeeCalculator\AbstractFeeCalculator;
use App\Interpolation\FeeCalculator\Collection\FeeCollection;
use App\Interpolation\FeeCalculator\Interpolation\InterpolationInterface;
use App\Interpolation\FeeCalculator\Loan\Exception\InvalidTermException;
use App\Interpolation\FeeCalculator\Loan\Exception\AmountAboveMaximumException;
use App\Interpolation\FeeCalculator\Loan\Exception\AmountBelowMinimumException;
use App\Interpolation\Model\LoanApplication;
use App\Interpolation\Storage\StorageInterface;

final class FeeCalculator extends AbstractFeeCalculator
{
    /** @var int  */
    private const FLOAT_PRECISION = 2;

    /** @var int[]  */
    private const PERMISSIBLE_TERMS = [12, 24];

    /** @var string */
    private const RESOURCE_NAME_TEMPLATE = '%d-month-loan.json';

    /** @var FeeCollection<string, int|float>|null  */
    private ?FeeCollection $feeCollection = null;

    public function __construct(
        private StorageInterface $storage,
        private InterpolationInterface $interpolation,
    ) {
    }

    public function calculate(): void
    {
        /** @var LoanApplication $application */
        $application = $this->getApplication();

        $feeCollection = $this->getFeesFromStorage($application->getTerm());

        $amount = round($application->getAmount(), precision: 2);

        /** There's a direct match */
        if (false !== $feeCollection->containsKey((string) $amount)) {
            $application->setFee($feeCollection[(string) $amount]);

            return;
        }

        $application->setFee(
            $this->getFeeForNoMatch($feeCollection, $amount)
        );
    }

    private function getFeesFromStorage(int $term): FeeCollection
    {
        if ($this->feeCollection instanceof FeeCollection) {
            return $this->feeCollection;
        }

        $resourceName = sprintf(self::RESOURCE_NAME_TEMPLATE, $term);

        // Should probably validate the returned data is actually what we're after
        $feeArray = $this->storage->fetch($resourceName);

        return $this->feeCollection = new FeeCollection($feeArray);
    }

    private function getFeeForNoMatch(FeeCollection $feeCollection, float $amount): float
    {
        $interpolatedFee = $this->interpolation->estimate($feeCollection, $amount);

        $total = bcadd(
            (string) $amount,
            (string) $interpolatedFee,
            self::FLOAT_PRECISION
        );

        $totalToNearest5 = ceil($total / 5) * 5;

        return (float) bcsub(
            (string) $totalToNearest5,
            (string) $amount,
            self::FLOAT_PRECISION
        );
    }

    public function validate(): void
    {
        /** @var LoanApplication $application */
        $application = $this->getApplication();

        $feeCollection = $this->getFeesFromStorage($application->getTerm());

        $values = $feeCollection->getKeys();

        if ($application->getAmount() < $values[0]) {
            throw new AmountBelowMinimumException();
        }

        if ($application->getAmount() > end($values)) {
            throw new AmountAboveMaximumException();
        }

        if (false === in_array($application->getTerm(), self::PERMISSIBLE_TERMS)) {
            throw new InvalidTermException();
        }
    }

    public function isSupported(): bool
    {
        return $this->application::class === LoanApplication::class;
    }
}
