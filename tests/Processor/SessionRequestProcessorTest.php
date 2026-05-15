<?php

declare(strict_types=1);

namespace Tests\Datalog\Processor;

use Datalog\Processor\SessionRequestProcessor;
use Monolog\LogRecord;
use Monolog\Level;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Datalog\TestCase;

class SessionRequestProcessorTest extends TestCase
{
    private SessionRequestProcessor $processor;

    public function setUp(): void
    {
        $this->processor = new SessionRequestProcessor(
            new RequestStack()
        );
    }

    public function testProcessesLogRecord(): void
    {
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'test message',
        );

        $result = ($this->processor)($record);

        $this->assertInstanceOf(LogRecord::class, $result);
        $this->assertArrayHasKey('request_id', $result->extra);
        $this->assertArrayHasKey('session_id', $result->extra);
    }

    public function testCleansParamKeys(): void
    {
        $params = [
            'foo' => 'bar',
            'test password test' => 'password',
            1 => 'one',
            'tester csrf_token tester' => 'csrf_token',
            'baz' => [
                'qux' => 'quux',
            ],
            'password' => 'password',
            'password test' => 'password',
        ];

        $cleanedParams = self::callPrivateMethod($this->processor, 'clean', $params);

        $this->assertSame([
            'foo' => 'bar',
            1 => 'one',
            'baz' => [
                'qux' => 'quux',
            ],
        ], $cleanedParams);
    }
}
