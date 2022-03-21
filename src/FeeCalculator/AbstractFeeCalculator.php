<?php

declare(strict_types=1);

namespace App\Interpolation\FeeCalculator;

use App\Interpolation\Model\Application;

abstract class AbstractFeeCalculator
{
    protected Application $application;

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * Is responsible for adding a fee to an Application
     */
    abstract public function calculate(): void;

    /**
     * @throws \RuntimeException
     */
    abstract public function validate(): void;

    abstract public function isSupported(): bool;
}
