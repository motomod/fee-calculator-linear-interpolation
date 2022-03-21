<?php

namespace App\Interpolation\Tests\FeeCalculator\Interpolation;

use App\Interpolation\FeeCalculator\Collection\FeeCollection;
use App\Interpolation\FeeCalculator\Interpolation\Exception\ValueOutOfRangeException;
use App\Interpolation\FeeCalculator\Interpolation\LinearInterpolation;
use PHPUnit\Framework\TestCase;

class LinearInterpolationTest extends TestCase
{
    /** @dataProvider estimationsWithResults */
    public function testEstimation(float $value, float $expected): void
    {
        $interpolator = new LinearInterpolation();

        $feeCollection = new FeeCollection([
            '1000' => 120,
            '2000' => 120,
            '3000' => 145,
            '4000' => 150,
            '5000' => 155,
            '6000' => 190,
        ]);

        $fee = $interpolator->estimate($feeCollection, $value);

        $this->assertEquals($expected, $fee);
    }

    public function estimationsWithResults(): array
    {
        return [
            [
                // value      // expected
                1221,         120,
            ],
            [
                // value      // expected
                2200,         125,
            ],
            [
                // value      // expected
                4242,         151.21,
            ],
            [
                // value      // expected
                5111,         158.885,
            ],
            [
                // value      // expected
                3531,         147.655,
            ],
        ];
    }

    public function testValueIsOutOfRange(): void
    {
        $interpolator = new LinearInterpolation();

        $feeCollection = new FeeCollection([
            '1000' => 120,
            '2000' => 120,
        ]);

        $this->expectException(ValueOutOfRangeException::class);

        $interpolator->estimate($feeCollection, 999999999);
    }
}
