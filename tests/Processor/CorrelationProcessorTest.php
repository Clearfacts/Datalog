<?php

declare(strict_types=1);

namespace Tests\Datalog\Processor;

use Datalog\Correlation\Correlation;
use Datalog\Processor\CorrelationProcessor;
use Monolog\Level;
use Monolog\LogRecord;
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

    public function testAddsCorrelationIdToRecord(): void
    {
        $this->correlation->expects($this->once())
            ->method('getId')
            ->willReturn('123');

        $record = $this->processor->__invoke(new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'test',
        ));

        $this->assertSame('123', $record->extra['correlation_id']);
    }
}
