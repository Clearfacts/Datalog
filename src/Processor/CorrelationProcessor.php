<?php

declare(strict_types=1);

namespace Datalog\Processor;

use Datalog\Correlation\Correlation;
use Monolog\Processor\ProcessorInterface;

class CorrelationProcessor implements ProcessorInterface
{
    private Correlation $correlation;

    public function __construct(Correlation $correlation)
    {
        $this->correlation = $correlation;
    }

    public function __invoke(array $record): array
    {
        $this->correlation->init();

        $record['extra']['correlation_id'] = $this->correlation::getId();

        return $record;
    }
}
