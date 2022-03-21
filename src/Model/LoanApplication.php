<?php

declare(strict_types=1);

namespace App\Interpolation\Model;

/**
 * A cut down version of a loan application containing
 * only the required properties for this test.
 */
final class LoanApplication extends Application
{
    private float $fee = 0.00;

    public function __construct(
        private int $term,
        private float $amount
    ) {
    }

    /**
     * Term (loan duration) for this loan application
     * in number of months.
     */
    public function getTerm(): int
    {
        return $this->term;
    }

    /**
     * Amount requested for this loan application.
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getFee(): float
    {
        return $this->fee;
    }

    public function setFee(float $fee): LoanApplication
    {
        $this->fee = $fee;

        return $this;
    }
}
