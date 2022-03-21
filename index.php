<?php

require __DIR__.'/vendor/autoload.php';

use App\Interpolation\Model\LoanApplication;
use App\Interpolation\FeeCalculator\Loan\FeeCalculator;
use App\Interpolation\FeeCalculatorManager;
use App\Interpolation\Storage\JsonFile;
use App\Interpolation\FeeCalculator\Interpolation\LinearInterpolation;

/**
 * These would ideally be tagged and auto-injected into the Calculator service
 */
$calculators = [
    new FeeCalculator(
        storage: new JsonFile(__DIR__.'/src/assets/fees'),
        interpolation: new LinearInterpolation()
    ),
];

$term = $argv[1];
$amount = $argv[2];

$loanApplication = new LoanApplication($term, $amount);

$feeCalculatorManager = new FeeCalculatorManager($calculators);
$feeCalculatorManager->calculate($loanApplication);

var_dump($loanApplication);
exit;
