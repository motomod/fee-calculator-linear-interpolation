<?php

declare(strict_types=1);

namespace App\Interpolation\Tests\FeeCalculator\Loan;

use App\Interpolation\FeeCalculator\Interpolation\InterpolationInterface;
use App\Interpolation\FeeCalculator\Loan\Exception\AmountAboveMaximumException;
use App\Interpolation\FeeCalculator\Loan\Exception\AmountBelowMinimumException;
use App\Interpolation\FeeCalculator\Loan\Exception\InvalidTermException;
use App\Interpolation\FeeCalculator\Loan\FeeCalculator;
use App\Interpolation\Model\Application;
use App\Interpolation\Model\LoanApplication;
use App\Interpolation\Storage\StorageInterface;
use PHPUnit\Framework\TestCase;

class FeeCalculatorTest extends TestCase
{
    private function getMockedLoanCalculator(?StorageInterface $storage = null, ?InterpolationInterface $interpolator = null): FeeCalculator
    {
        if (null === $storage) {
            $storage = $this->getMockBuilder(StorageInterface::class)
                ->onlyMethods(['fetch'])
                ->getMock();

            $storage->expects(self::once())->method('fetch')->willReturn([
                '1000' => 100,
                '2000' => 125,
            ]);
        }

        if (null === $interpolator) {
            $interpolator = $this->getMockBuilder(InterpolationInterface::class)
                ->onlyMethods(['estimate'])
                ->getMock();

            $interpolator->method('estimate')->willReturn(112.00);
        }

        return new FeeCalculator($storage, $interpolator);
    }

    public function testCalculatorReturnsADirectlyMatchedFee()
    {
        $loanApplication = new LoanApplication(
            12,
            1000
        );

        $feeCalculator = $this->getMockedLoanCalculator();
        $feeCalculator->setApplication($loanApplication);
        $feeCalculator->calculate();

        $this->assertEquals(100, $loanApplication->getFee());
    }

    public function testCalculatorReturnsAFeeWhenNoDirectMatch(): void
    {
        $loanApplication = new LoanApplication(
            12,
            1231
        );

        $feeCalculator = $this->getMockedLoanCalculator();
        $feeCalculator->setApplication($loanApplication);
        $feeCalculator->calculate();

        $this->assertEquals(114, $loanApplication->getFee());
    }

    public function testIsSupportsReturnsTrueForMatchedApplication(): void
    {
        $loanApplication = new LoanApplication(
            12,
            1231
        );

        $feeCalculator = $this->getMockedLoanCalculator($this->createMock(StorageInterface::class));
        $feeCalculator->setApplication($loanApplication);

        $this->assertTrue($feeCalculator->isSupported());
    }

    public function testIsSupportsReturnsFalseForInvalidApplication(): void
    {
        $badApplication = $this->getMockBuilder(Application::class)
            ->getMock();

        $feeCalculator = $this->getMockedLoanCalculator($this->createMock(StorageInterface::class));
        $feeCalculator->setApplication($badApplication);

        $this->assertFalse($feeCalculator->isSupported());
    }

    /** @dataProvider invalidLoanApplicationAmountsProvider */
    public function testLoanApplicationValidation(int $term, float $amount, string $validationException): void
    {
        $loanApplication = new LoanApplication(
            $term,
            $amount
        );

        $feeCalculator = $this->getMockedLoanCalculator();
        $feeCalculator->setApplication($loanApplication);

        $this->expectException($validationException);

        $feeCalculator->validate();
    }

    private function invalidLoanApplicationAmountsProvider(): array
    {
        return [
            [   // term  // amount      // exception
                12,      22031,         AmountAboveMaximumException::class,
            ],
            [
                12,      4,             AmountBelowMinimumException::class,
            ],
            [
                13,      1050,          InvalidTermException::class,
            ],
        ];
    }

    /** @dataProvider interpolationResults */
    public function testFeeIsRoundedUpSoTotalIsMultipleOf5(float $amount, float $interpolationResult, float $calculatedFee)
    {
        $loanApplication = new LoanApplication(
            12,
            $amount
        );

        $interpolator = $this->getMockBuilder(InterpolationInterface::class)
            ->onlyMethods(['estimate'])
            ->getMock();

        $interpolator->method('estimate')->willReturn($interpolationResult);

        $feeCalculator = $this->getMockedLoanCalculator(
            interpolator: $interpolator
        );
        $feeCalculator->setApplication($loanApplication);
        $feeCalculator->calculate();

        $this->assertEquals($calculatedFee, $loanApplication->getFee());
    }

    private function interpolationResults(): array
    {
        return [
            [   // amount   // interpolation result   // fee result
                1250,       2,                        5.00
            ],
            [   // amount   // interpolation result   // fee result
                1250,       2.12,                     5.00
            ],
            [   // amount   // interpolation result   // fee result
                1248.55,    1.45,                     1.45
            ],
            [   // amount   // interpolation result   // fee result
                1250.46363, 2,                        4.54
            ],
        ];
    }
}
