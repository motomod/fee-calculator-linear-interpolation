<?php

namespace App\Interpolation\Tests;

use App\Interpolation\FeeCalculator\AbstractFeeCalculator;
use App\Interpolation\FeeCalculator\Exception\MissingFeeCalculatorException;
use App\Interpolation\FeeCalculator\Exception\ValidationException;
use App\Interpolation\FeeCalculatorManager;
use App\Interpolation\Model\LoanApplication;
use PHPUnit\Framework\TestCase;

final class FeeCalculatorManagerTest extends TestCase
{
    private function getTestApplication(): LoanApplication
    {
        return new LoanApplication(
            term: 12,
            amount: 1234
        );
    }

    public function testSupportedAndValidCalculatorIsExecuted()
    {
        $mockCalculator = $this->getMockBuilder(AbstractFeeCalculator::class)
            ->onlyMethods(['isSupported', 'calculate'])
            ->getMockForAbstractClass();

        $mockCalculator->expects(self::once())->method('isSupported')->willReturn(true);
        $mockCalculator->expects(self::once())->method('calculate');

        $calculatorManager = new FeeCalculatorManager([
            $mockCalculator
        ]);

        $calculatorManager->calculate($this->getTestApplication());
    }

    public function testUnsupportedCalculatorIsNotExecuted()
    {
        $mockCalculator = $this->getMockBuilder(AbstractFeeCalculator::class)
            ->onlyMethods(['isSupported', 'calculate'])
            ->getMockForAbstractClass();

        $mockCalculator->expects(self::once())->method('isSupported')->willReturn(false);
        $mockCalculator->expects(self::never())->method('calculate');

        $calculatorManager = new FeeCalculatorManager([
            $mockCalculator
        ]);

        $this->expectException(MissingFeeCalculatorException::class);

        $calculatorManager->calculate($this->getTestApplication());
    }

    public function testApplicationValidationFailsCalculatorIsNotExecuted()
    {
        $mockCalculator = $this->getMockBuilder(AbstractFeeCalculator::class)
            ->onlyMethods(['isSupported', 'validate', 'calculate'])
            ->getMockForAbstractClass();

        $mockCalculator->expects(self::once())->method('isSupported')->willReturn(true);
        $mockCalculator->expects(self::once())->method('validate')->willThrowException(new ValidationException());
        $mockCalculator->expects(self::never())->method('calculate');

        $calculatorManager = new FeeCalculatorManager([
            $mockCalculator
        ]);

        $this->expectException(ValidationException::class);

        $calculatorManager->calculate($this->getTestApplication());
    }

    public function testNoCalculatorsResultsInMissingCalculatorException()
    {
        $calculatorManager = new FeeCalculatorManager([]);

        $this->expectException(MissingFeeCalculatorException::class);

        $calculatorManager->calculate($this->getTestApplication());
    }
}
