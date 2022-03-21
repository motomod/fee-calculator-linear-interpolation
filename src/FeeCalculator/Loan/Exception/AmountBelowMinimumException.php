<?php

declare(strict_types=1);

namespace App\Interpolation\FeeCalculator\Loan\Exception;

use App\Interpolation\FeeCalculator\Exception\ValidationException;

final class AmountBelowMinimumException extends ValidationException
{
}
