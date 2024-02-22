<?php

declare(strict_types=1);

namespace Tests\Datalog\Processor;

use Datalog\Correlation\Correlation;
use Datalog\Processor\CorrelationProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Datalog\TestCase;

class CorrelationProcessorTest extends TestCase
{
    private CorrelationProcessor $processor;
    private Correlation|MockObject $correlation;

    public function setUp(): void
    {
        $this->processor = new CorrelationProcessor(
            $this->correlation = $this->createMock(Correlation::class)
        );
    }

    public function testAddsCorrelationId(): void
    {
        $this->correlation->expects($this->once())
            ->method('getId')
            ->willReturn('123');

        $record = $this->processor->__invoke([]);

        $this->assertSame([
            'extra' => [
                'correlation_id' => '123',
            ],
        ], $record);
    }
}